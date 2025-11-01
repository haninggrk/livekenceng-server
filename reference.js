#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const axios = require('axios');
const cliProgress = require('cli-progress');
const colors = require('colors');
const { program } = require('commander');

// Color theme
colors.setTheme({
  info: 'cyan',
  success: 'green',
  warning: 'yellow',
  error: 'red',
  highlight: 'magenta',
  progress: 'blue'
});

class ShopeeProductSwitcher {
  constructor() {
    this.rl = require('readline').createInterface({
      input: process.stdin,
      output: process.stdout
    });
  }

  /**
   * Get session IDs from Shopee Creator API
   */
  async getSessionIdsFromAPI() {
    try {
      console.log(`\n${colors.info('Fetching session IDs from Shopee Creator API...')}`);
      
      const cookie = fs.readFileSync('cookie.txt', 'utf8').trim();
      
      const response = await axios.get('https://creator.shopee.co.id/supply/api/lm/sellercenter/realtime/sessionList?page=1&pageSize=10&name=&orderBy=&sort=', {
        headers: {
          'Cookie': cookie,
          'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
          'Accept': 'application/json, text/plain, */*',
          'Accept-Language': 'id-ID,id;q=0.9,en;q=0.8',
          'Referer': 'https://creator.shopee.co.id/',
          'Origin': 'https://creator.shopee.co.id'
        }
      });

      if (response.data.code === 0 && response.data.data && response.data.data.list) {
        const sessionIds = response.data.data.list.map(record => record.sessionId).filter(id => id);
        
        if (sessionIds.length > 0) {
          console.log(`${colors.success('✓')} Found ${sessionIds.length} session ID(s): ${sessionIds.join(', ')}`);
          return sessionIds;
        } else {
          console.log(`${colors.warning('⚠')} No session IDs found in API response`);
          return [];
        }
      } else if (response.data.code === 100003) {
        console.log(`${colors.error('✗')} Authentication failed - please check your cookie.txt file`);
        return [];
      } else {
        console.log(`${colors.error('✗')} API returned error code: ${response.data.code}`);
        return [];
      }
    } catch (error) {
      console.log(`${colors.error('✗')} Failed to fetch session IDs: ${error.message}`);
      return [];
    }
  }

  /**
   * Select session ID from fetched list
   */
  async selectSessionId(sessionIds) {
    if (sessionIds.length === 0) {
      console.log(`${colors.warning('⚠')} No session IDs available from API`);
      return null;
    }

    if (sessionIds.length === 1) {
      console.log(`${colors.success('✓')} Using single session ID: ${sessionIds[0]}`);
      return sessionIds[0];
    }

    console.log(`\n${colors.info('Available Session IDs:')}`);
    sessionIds.forEach((sessionId, index) => {
      console.log(`${index + 1}. ${colors.highlight(sessionId)}`);
    });

    return new Promise((resolve) => {
      this.rl.question(`\n${colors.info('Select session ID (number): ')}`, (answer) => {
        const selectedIndex = parseInt(answer) - 1;
        
        if (selectedIndex >= 0 && selectedIndex < sessionIds.length) {
          console.log(`${colors.success('✓')} Selected session ID: ${sessionIds[selectedIndex]}`);
          resolve(sessionIds[selectedIndex]);
        } else {
          console.log(`${colors.error('Invalid selection! Using first session ID.')}`);
          resolve(sessionIds[0]);
        }
      });
    });
  }

  /**
   * Get product sets from ProductSwitch folder
   */
  getProductSets() {
    const productSwitchDir = 'ProductSwitch';
    
    if (!fs.existsSync(productSwitchDir)) {
      console.log(`${colors.warning('⚠')} ProductSwitch folder not found! Creating it...`);
      fs.mkdirSync(productSwitchDir);
      console.log(`${colors.info('✓')} Created ProductSwitch folder. Please add txt files with product URLs.`);
      return [];
    }

    const files = fs.readdirSync(productSwitchDir);
    const txtFiles = files.filter(file => file.endsWith('.txt'));

    if (txtFiles.length === 0) {
      console.log(`${colors.warning('⚠')} No txt files found in ProductSwitch folder!`);
      return [];
    }

    const productSets = [];
    
    for (const file of txtFiles) {
      try {
        const filePath = path.join(productSwitchDir, file);
        const content = fs.readFileSync(filePath, 'utf8').trim();
        
        if (!content) {
          console.log(`${colors.warning('⚠')} Empty file: ${file}`);
          continue;
        }

        const urls = content.split('\n')
          .map(line => line.trim())
          .filter(line => line.length > 0)
          .filter(line => line.includes('shopee.co.id/product/'));

        if (urls.length === 0) {
          console.log(`${colors.warning('⚠')} No valid URLs in: ${file}`);
          continue;
        }

        // Parse URLs and extract shop_id and item_id
        const parsedUrls = [];
        for (const url of urls) {
          const urlMatch = url.match(/\/product\/(\d+)\/(\d+)/);
          if (urlMatch) {
            const shopId = parseInt(urlMatch[1]);
            const itemId = parseInt(urlMatch[2]);
            
            parsedUrls.push({
              url,
              shopId,
              itemId,
              name: `Product ${shopId}/${itemId}`
            });
          }
        }

        if (parsedUrls.length > 0) {
          productSets.push({
            name: file.replace('.txt', ''),
            file: file,
            products: parsedUrls
          });
        }
      } catch (error) {
        console.log(`${colors.error('✗')} Error reading ${file}: ${error.message}`);
      }
    }

    return productSets;
  }

  /**
   * Get switch interval from user
   */
  async getSwitchInterval() {
    return new Promise((resolve) => {
      this.rl.question(`\n${colors.info('Enter switch interval in seconds (default: 30): ')}`, (answer) => {
        const trimmedAnswer = answer.trim();
        
        if (trimmedAnswer === '') {
          console.log(`${colors.success('✓')} Using default switch interval: 30 seconds`);
          resolve(30);
        } else {
          const seconds = parseInt(trimmedAnswer);
          if (isNaN(seconds) || seconds < 5) {
            console.log(`${colors.error('Invalid interval! Using default 30 seconds.')}`);
            resolve(30);
          } else {
            console.log(`${colors.success('✓')} Switch interval set: ${seconds} seconds`);
            resolve(seconds);
          }
        }
      });
    });
  }

  /**
   * Replace all products in live stream using PUT API
   */
  async replaceProductsInLiveStream(items, sessionId) {
    try {
      console.log(`${colors.info(`Replacing products in live stream with ${items.length} products...`)}`);
      
      const cookie = fs.readFileSync('cookie.txt', 'utf8').trim();
      
      const headers = {
        'accept': '*/*',
        'accept-encoding': 'gzip, deflate, br',
        'accept-language': 'id-ID,id,en-US,en',
        'content-type': 'application/json',
        'Cookie': cookie,
        'user-agent': 'language=id app_type=1 platform=native_ios appver=35945 os_ver=18.6.2 Cronet/102.0.5005.61',
        'x-livestreaming-source': 'shopee',
        'x-shopee-client-timezone': 'Asia/Jakarta'
      };

      const response = await axios.put(`https://live.shopee.co.id/api/v1/session/${sessionId}/items`, {
        items: items.map(item => ({
          shop_id: item.shopId,
          item_id: item.itemId
        }))
      }, {
        headers,
        timeout: 15000
      });

      if (response.status === 200) {
        console.log(`${colors.success('✓')} Successfully replaced products in live stream`);
        return true;
      } else {
        console.log(`${colors.error('✗')} Failed to replace products. Status: ${response.status}`);
        return false;
      }
    } catch (error) {
      console.log(`${colors.error('✗')} Failed to replace products: ${error.message}`);
      if (error.response) {
        console.log(`${colors.error('API Error Response:')}`, error.response.data);
      }
      return false;
    }
  }


  /**
   * Start automatic switching between product sets
   */
  async startAutomaticSwitching(productSets, sessionId, switchInterval) {
    console.log(`\n${colors.success('🚀 Starting automatic switching...')}`);
    console.log(`${colors.info('Press Ctrl+C to stop switching')}`);

    let currentSetIndex = 0;
    let currentProducts = [];

    // Handle Ctrl+C gracefully
    process.on('SIGINT', async () => {
      console.log(`\n${colors.warning('⚠')} Stopping automatic switching...`);
      
      // Clean up - clear all products
      if (currentProducts.length > 0) {
        console.log(`${colors.info('Cleaning up - clearing all products...')}`);
        await this.replaceProductsInLiveStream([], sessionId);
      }
      
      console.log(`${colors.success('✓')} Automatic switching stopped.`);
      process.exit(0);
    });

    try {
      while (true) {
        const currentSet = productSets[currentSetIndex];
        console.log(`\n${colors.info(`[${new Date().toLocaleTimeString()}] Switching to set: ${currentSet.name}`)}`);

        // Replace all products with new set
        console.log(`${colors.info(`Switching to ${currentSet.products.length} products from ${currentSet.name}...`)}`);
        const success = await this.replaceProductsInLiveStream(currentSet.products, sessionId);
        
        if (success) {
          currentProducts = currentSet.products.map(p => p.itemId);
          console.log(`${colors.success('✓')} Successfully switched to ${currentSet.name}`);
        } else {
          console.log(`${colors.error('✗')} Failed to switch to ${currentSet.name}`);
        }

        // Wait for next switch
        console.log(`${colors.info(`Waiting ${switchInterval} seconds for next switch...`)}`);
        await new Promise(resolve => setTimeout(resolve, switchInterval * 1000));

        // Move to next set
        currentSetIndex = (currentSetIndex + 1) % productSets.length;
      }
    } catch (error) {
      console.log(`\n${colors.error('Error during automatic switching:')} ${error.message}`);
      
      // Clean up - clear all products
      if (currentProducts.length > 0) {
        console.log(`${colors.info('Cleaning up - clearing all products...')}`);
        await this.replaceProductsInLiveStream([], sessionId);
      }
    }
  }

  /**
   * Main process
   */
  async run() {
    try {
      console.log(`${colors.highlight('🔄 Shopee Product Switcher')}`);
      console.log(`${colors.info('Automatically switch products in your live stream')}\n`);

      // Check if cookie.txt exists
      if (!fs.existsSync('cookie.txt')) {
        console.log(`${colors.error('✗')} cookie.txt file not found!`);
        console.log(`${colors.warning('Please add your Shopee cookie to cookie.txt file')}`);
        return;
      }

      // Get product sets
      const productSets = this.getProductSets();
      if (productSets.length === 0) {
        console.log(`${colors.error('No valid product sets found!')}`);
        return;
      }

      console.log(`\n${colors.success('✓')} Found ${productSets.length} product sets:`);
      productSets.forEach((set, index) => {
        console.log(`${index + 1}. ${colors.highlight(set.name)} (${set.products.length} products)`);
      });

      // Get session IDs from API
      const sessionIds = await this.getSessionIdsFromAPI();
      if (sessionIds.length === 0) {
        console.log(`${colors.error('No session IDs available!')}`);
        return;
      }

      // Select session ID
      const sessionId = await this.selectSessionId(sessionIds);
      if (!sessionId) {
        console.log(`${colors.error('No session ID selected!')}`);
        return;
      }

      // Get switch interval
      const switchInterval = await this.getSwitchInterval();

      // Show summary
      console.log(`\n${colors.info('Automatic Switch Summary:')}`);
      console.log(`${colors.info('• Live Stream Session:')} ${sessionId}`);
      console.log(`${colors.info('• Switch Interval:')} ${switchInterval} seconds`);
      console.log(`${colors.info('• Product Sets:')} ${productSets.length}`);
      console.log(`${colors.info('• Total Products:')} ${productSets.reduce((sum, set) => sum + set.products.length, 0)}`);

      // Ask for confirmation
      this.rl.question(`\n${colors.warning('Start automatic switching? (y/n): ')}`, async (answer) => {
        if (answer.toLowerCase().trim() === 'y' || answer.toLowerCase().trim() === 'yes') {
          await this.startAutomaticSwitching(productSets, sessionId, switchInterval);
        } else {
          console.log(`${colors.info('Operation cancelled.')}`);
        }
        this.close();
      });
    } catch (error) {
      console.log(`${colors.error('Error:')} ${error.message}`);
      this.close();
    }
  }

  /**
   * Close readline interface
   */
  close() {
    this.rl.close();
  }
}

// CLI setup
program
  .name('shopee-product-switcher')
  .description('Automatically switch products in Shopee Live Stream')
  .version('1.0.0');

program
  .action(async () => {
    const switcher = new ShopeeProductSwitcher();
    await switcher.run();
  });

program.parse();

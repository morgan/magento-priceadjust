# Magento PriceAdjust

PriceAdjust is an advanced tool for mass updating products using rules in Magento. Below is a comprehensive list of features:

- Select individual or all products 

## Getting Started

After installation, to get started with PriceAdjust:

1. Login to Magento Admin Panel
2. Go to `Catalog`, `Manage Products`
3. Select individual products or `Select All`
4. Choose `Price Adjust` from `Actions` dropdown and click `Submit`
5. From here, rules can be added based on weight. If simple addition or subtraction is desired for all products, set `Begin Weight` to `0` and `End Weight` to a very large number. From there, the type of arithmetic can be chosen. 
6. Be sure to `Preview` the adjustment before commiting and clicking `Mass Adjust`

Important: It is recommended and best practice to backup your database before performing any mass action in a production environment. 

## Version 0.1.0

This is release version 0.1.0 of [PriceAdjust](https://github.com/morgan/magento-priceadjust).

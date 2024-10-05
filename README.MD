# OpenCart 3 Fintreen Payment Module

## Overview

This module integrates the Fintreen cryptocurrency payment gateway with OpenCart 3, allowing customers to pay for orders using various cryptocurrencies. It provides a seamless way to accept crypto payments in your OpenCart 3 store.

## Features

- Cryptocurrency payment option in checkout
- Configurable from OpenCart admin panel
- Test mode for development and testing
- Webhook support for transaction status updates
- Compatible with OpenCart 3.x

## Requirements

- OpenCart 3.x
- PHP 7.3 or higher
- cURL enabled
- Fintreen account with API credentials

## Installation

1. Download the module files.
2. Upload the contents of the `upload` folder to your OpenCart root directory. This will add files to the `admin`, `catalog`, and `system` directories.
3. Log in to your OpenCart admin panel.
4. Navigate to Extensions > Extensions.
5. Choose "Payments" from the extension type filter.
6. Find "Fintreen Payment" in the list and click the Install button (green +).

## Configuration

1. After installation, click the Edit button (blue pencil) next to Fintreen Payment.
2. Configure the following settings:
    - **Fintreen Token**: Enter your Fintreen API token.
    - **Fintreen Email**: Enter the email associated with your Fintreen account.
    - **Test Mode**: Enable for testing (no real transactions will be made).
    - **Total**: Minimum order total for this payment method to be available.
    - **Order Status**: Select the status for orders paid with Fintreen.
    - **Geo Zone**: Select the geo zone where this payment method will be available.
    - **Status**: Enable or disable the payment method.
    - **Sort Order**: Set the display order of this payment method in checkout.
3. Click Save to store your settings.

## Usage

Once configured, the Fintreen payment option will appear during the checkout process. Customers can select it to pay with cryptocurrencies.

### Webhook

To receive automatic updates on transaction statuses, set up a webhook in your Fintreen account:

1. Log in to your Fintreen account.
2. Navigate to the API settings.
3. Set the webhook URL to: `https://your-store-url.com/index.php?route=extension/payment/fintreen/callback`

Replace `your-store-url.com` with your actual OpenCart store URL.

## Troubleshooting

- If the payment method doesn't appear in checkout, check if the order total meets the minimum amount set in the module configuration.
- Ensure your server has cURL enabled and can make outgoing connections to the Fintreen API.
- Check the OpenCart error logs for any specific error messages.

## Support

For issues, questions, or contributions, please open an issue in this repository or contact our support team at support@yourcompany.com.

## License

[MIT License](LICENSE.md)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

Remember to always keep your OpenCart installation and this module up to date for security and compatibility reasons.
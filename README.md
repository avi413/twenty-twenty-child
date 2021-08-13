<br />
<p align="center">
  <h3 align="center">wordpress twenty-twenty-child Theme</h3>
</p>


# Child theme

This is a sample child theme created mainly for DEMO.

## Download

You can download the [installable theme ZIP package](https://github.com/avi413/twenty-twenty-child/archive/refs/heads/main.zip), the `twenty-twenty-child-main.zip` file directly from [this repository](https://github.com/avi413/twenty-twenty-child/).  
***Read the instructions below for how to set the theme up before installing!***

<!-- SET THE THEME -->
## How to set the child theme?

1. Unzip the `twenty-twenty-child-main.zip` file on your computer.
2. Please make sure that the mother theme twenty-twenty installed on your WordPress site.
3. Now upload the child theme via FTP to `YOUR_WORDPRESS_INSTALLATION_FOLDER/wp-content/themes/` folder.  
  (Or ZIP your child theme and upload it via WordPress dashboard).
4. In your WordPress dashboard navigate to **Appearance &raquo; Themes** and activate your child theme.
 

### what will this child theme do?

1. Create a new editor user.
2. Create a new `product` post type.
3. Create 6 demo product items.
4. Create a shortcode to display box of product for Example : `[product_box bg_color=#7ac4f3 product_id=3279 ]`
5. Have custom filter to overide the shortcode output
6. Mobile address bar colored by #7ac4f3
7. Custom json api  to get list of product from given category name/id  Use -   `http://<wehbhost>/devtest/wp-json/twentytwent-child/v1/product-list/<category ID/name>`

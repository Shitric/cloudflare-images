
# CloudFlare Images
A simple PHP class for CloudFlare Images CDN.

# Dependencies

1. PHP 7.3+
2. cURL Library

# Installation

**With composer**

    composer require shitric/cloudflare-images

**Manual**

- Download the CloudFlareImages.php file from src/Images.php
- Include your project with require or require_once functions. Choose what if you want.

# Usage

**Creating new instance.**

You can get the construct parameters from your CloudFlare account.

    $cloudFlareImages = new Images('Account_ID', 'API_Token', 'Images_Hash', 'Domain');

**Image Uploading**

I highly recommend using it with the try-catch block. Because this method throws Exception for cURL.
Last parameter for delete image file after the uploading. It's optional, default value is false.

Returns Image ID for getting the uploaded image from CDN

    $imageId = $cloudFlareImages->uploadImageFile('File_Path', 'File_Name', false)

**Getting the Uploaded Image URL**

Just give the Image ID and it will returns the direct Image url. Second parameter for image variant. Default is **public**.

    $cloudFlareImages->getImageUrl('Image_ID', 'Variant')

**Deleting the Image from CDN**

Returns **True** or **False**.

    $cloudFlareImages->deleteImage('Image_ID')

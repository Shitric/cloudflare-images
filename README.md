
# CloudFlare Images
A simple PHP class for CloudFlare Images CDN.

# Dependencies

1. PHP 7.3+
2. cURL Library

# Installation

**With composer**

    composer require shitric/cloudflare-images

**Manual**

- Download the Images.php file from src
- Include your project with require or require_once functions. Choose what if you want.

# Usage

**Creating new instance.**

You can get the construct parameters from your CloudFlare account.

```PHP
$cloudFlareImages = new Images('Account_ID', 'API_Token', 'Images_Hash', 'Domain');
```

**Image Uploading**

I highly recommend using it with the try-catch block. Because this method throws Exception for cURL.  
- 3rd parameter for delete image file after the uploading. Default value is `false`.
- 4th parameter for metadata. Default value is `[]`.
- 5th parameter for custom ID (max 1024 alpha-numeric chars). Default is `''` and lets Cloudflare generate the image ID. See
  [Cloudflare Docs](https://developers.cloudflare.com/images/cloudflare-images/upload-images/custom-id/)

Returns Image ID for getting the uploaded image from CDN

```PHP
$imageId = $cloudFlareImages->uploadImageFile('File_Path', 'File_Name', false, ['key'=>'value'], 'Custom_ID');
```

**Getting the Uploaded Image URL**

Just give the Image ID and it will returns the direct Image url. Second parameter for image variant. Default is **public**.

```PHP
$cloudFlareImages->getImageUrl('Image_ID', 'Variant');
```

**Deleting the Image from CDN**

Returns **True** or **False**.

```PHP
$cloudFlareImages->deleteImage('Image_ID');
```

<?php

namespace Shitric\CloudFlare;

use Exception;

class Images
{
    /**
     * Cloudflare Account ID.
     * @var string
     */
    protected string $cf_account_id;

    /**
     * Cloudflare API Token.
     *
     * @var string
     */
    protected string $cf_api_token;

    /**
     * CloudFlare Images Hash.
     *
     * @var string
     */
    protected string $cf_images_hash;

    /**
     * Domain for Cloudflare Images.
     * @var string
     */
    protected string $domain;

    /**
     * Set the Cloudflare Account ID, API Token and Images Hash while initializing the class.
     *
     * @param string $cf_account_id
     * @param string $cf_api_token
     * @param string $cf_images_hash
     * @param string $domain
     */
    public function __construct(string $cf_account_id, string $cf_api_token, string $cf_images_hash, string $domain)
    {
        $this->cf_account_id = $cf_account_id;
        $this->cf_api_token = $cf_api_token;
        $this->cf_images_hash = $cf_images_hash;
        $this->domain = $domain;
    }

    /**
     * Makes a POST request to the given URL and returns the response.
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return string
     * @throws Exception
     */
    private function post(string $url, array $data = [], array $headers = []): string
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception("cURL Error: " . curl_error($ch));
            } else {
                return $response;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Makes a DELETE request to the given URL and returns the response.
     *
     * @param string $url
     * @param array $headers
     * @return string
     * @throws Exception
     */
    private function delete(string $url, array $headers = []): string
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception("cURL Error: " . curl_error($ch));
            } else {
                return $response;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Uploads an image to Cloudflare Images and returns the image id.
     *
     * @param string $image
     * @param string $name
     * @param bool $delete_after_upload (optional)
     * @param array $meta_data (optional)
     * @param string $custom_id (optional - max 1024 characters)
     * @return string
     * @throws Exception
     */
    public function uploadImageFile(string $image, string $name, bool $delete_after_upload = false, array $meta_data = [], string $custom_id = ''): string
    {
        try {
            $payload = [
                'file' => new \CURLFile($image, mime_content_type($image), $name),
            ];
            if ($meta_data) {              
                $payload['metadata'] = json_encode($meta_data);
            }
            if ($custom_id) { 
                if (strlen($custom_id) > 1024) {
                    throw new Exception('$custom_id must be <= 1024 characters.');
                }
                $payload['id'] = $custom_id;
            }
            $response = $this->post("https://api.cloudflare.com/client/v4/accounts/{$this->cf_account_id}/images/v1", $payload, ["Authorization: Bearer {$this->cf_api_token}"]);

            $response = json_decode($response);
            if ($response->success) {
                if ($delete_after_upload) {
                    unlink($image);
                }

                return $response->result->id;
            } else {
                throw new Exception($response->errors[0]->message);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Deletes an image from Cloudflare Images.
     *
     * @param string $image_id
     * @return bool
     */
    public function deleteImage(string $image_id): bool
    {
        try {
            $response = $this->delete("https://api.cloudflare.com/client/v4/accounts/{$this->cf_account_id}/images/v1/{$image_id}", [
                "Authorization: Bearer {$this->cf_api_token}"
            ]);
            $response = json_decode($response);
            if ($response->success) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Gets the image url for the given image id.
     *
     * @param string $image_id
     * @param string $variant
     * @return string
     */
    public function getImageUrl(string $image_id, string $variant = 'public'): string
    {
        return "{$this->domain}/cdn-cgi/imagedelivery/{$this->cf_images_hash}/{$image_id}/{$variant}";
    }
}

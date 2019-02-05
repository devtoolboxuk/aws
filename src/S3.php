<?php

namespace devtoolboxuk\aws;

use \Aws\S3\S3Client;

/**
 * Class S3
 * Simple Class to connect to S3.
 */
class S3
{
    /**
     * @var object
     */
    protected $s3Client;

    /**
     * @var
     */
    protected $bucket;

    function __construct()
    {
        $this->s3Client = S3Client::factory();
    }

    public function getBucket()
    {
        return $this->bucket;
    }

    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @param $localFilename
     * @param $s3filename
     * @param string $acl
     */
    public function putObject($localFilename, $s3filename, $acl = 'bucket-owner-full-control')
    {
        $data = [
            'ACL' => $acl,
            'Bucket' => $this->bucket,
            'Key' => $s3filename,
            'SourceFile' => $localFilename
        ];

        try {
            $this->s3Client->putObject($data);
        } catch (S3Exception $e) {
            throw new \S3Exception(sprintf("Failed to upload file '%s' to S3.", $s3filename));
        }
    }

    public function getObject($localFilename, $s3filename)
    {

        $data = [
            'Bucket' => $this->bucket,
            'Key' => $s3filename,
            'SaveAs' => $localFilename
        ];

        try {
            $this->s3Client->getObject($data);
        } catch (S3Exception $e) {
            throw new RuntimeException(sprintf("Failed to get file '%s' from S3.", $s3filename));
        }
    }

    public function removeObject($s3filename)
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $s3filename
            ]);
        } catch (S3Exception $e) {
            throw new RuntimeException(sprintf("Failed to delete file '%s' from S3.", $s3filename));
        }
    }

}
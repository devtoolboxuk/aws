<?php

namespace devtoolboxuk\aws;

use \Aws\S3\S3Client;

class AwsS3
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

    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    public function getBucket()
    {
        return $this->bucket;
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

    /**
     * Saves an object to a local location
     * @param $s3filename
     * @param $localFilename
     */
    public function saveObject($s3filename, $localFilename)
    {
        try {
            $this->s3Client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $s3filename,
                'SaveAs' => $localFilename
            ]);
        } catch (S3Exception $e) {
            throw new \S3Exception(sprintf("Failed to get file '%s' from S3.", $s3filename));
        }
    }

    /**
     * Reads an object of off the S3 Bucket
     * @param $s3filename
     * @return mixed
     */
    public function getObject($s3filename)
    {
        try {
            $result = $this->s3Client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $s3filename
            ]);
            return $result['Body'];
        } catch (S3Exception $e) {
            throw new \S3Exception(sprintf("Failed to read file '%s' from S3.", $s3filename));
        }
    }

    public function listObjects($folder)
    {
        try {
            return $this->s3Client->getIterator('ListObjects', array(
                "Bucket" => $this->bucket,
                "Prefix" => $folder
            ));
        } catch (S3Exception $e) {
            throw new \S3Exception(sprintf("Failed to list objects in '%s' from S3.", $this->bucket));
        }
    }

    /**
     * Alternative name for deleteObject
     * @param $s3filename
     */
    public function removeObject($s3filename)
    {
        return $this->deleteObject($s3filename);
    }

    /**
     * @param $s3filename
     */
    public function deleteObject($s3filename)
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $s3filename
            ]);
        } catch (S3Exception $e) {
            throw new \S3Exception(sprintf("Failed to delete file '%s' from S3.", $s3filename));
        }
    }
}
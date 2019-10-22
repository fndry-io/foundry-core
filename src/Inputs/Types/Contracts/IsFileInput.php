<?php

namespace Foundry\Core\Inputs\Types\Contracts;

use Illuminate\Http\UploadedFile;

interface IsFileInput {

    /**
     * @return UploadedFile
     */
    public function getFile();

    /**
     * @param mixed $file
     */
    public function setFile( $file ): void;

    /**
     * @param UploadedFile $file The uploaded file
     * @param array $inputs The inputs from the request
     * @param boolean $is_public If the file is public
     *
     * @return IsFileInput
     */
    static function fromUploadedFile(UploadedFile $file, array $inputs = [], $is_public = false);

}

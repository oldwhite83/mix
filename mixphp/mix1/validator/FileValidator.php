<?php

/**
 * FileValidator类
 * @author 刘健 <code.liu@qq.com>
 */

namespace mix\validator;

class FileValidator extends BaseValidator
{

    // 允许的功能集合
    protected $allowActions = ['type', 'mimes', 'maxSize'];

    // 文件类型描述
    protected $fileTypeDesc = '文件';

    // 获取属性值
    protected function getAttributeValue()
    {
        return \Mix::$app->request->files($this->attribute);
    }

    // 类型验证
    protected function type()
    {
        $this->uploadError();
        // 创建文件对象
        
    }

    // 上传错误效验
    protected function uploadError()
    {
        $value = $this->attributeValue;
        if ($value['error'] > 0) {
            switch ($value['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $errorMsg = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值.';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMsg = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMsg = '文件只有部分被上传.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMsg = '没有文件被上传.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorMsg = '找不到临时文件夹.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMsg = '文件写入失败.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errorMsg = '文件上传扩展错误.';
                    break;
                default:
                    $errorMsg = '未知上传错误.';
                    break;
            }
            $this->errors[] = $errorMsg;
            return false;
        }
        return true;
    }

    // MIME类型验证
    protected function mimes($param)
    {
        $value = $this->attributeValue;
        if (!in_array($value['type'], $param)) {
            if (is_null($this->attributeMessage)) {
                $error = "{$this->fileTypeDesc}类型不在%s范围内.";
            } else {
                $error = "{$this->attributeLabel}{$this->attributeMessage}";
            }
            $this->errors[] = sprintf($error, implode('、', $param));
            return false;
        }
        return true;
    }

    // 最大文件大小效验
    protected function maxSize($param)
    {
        $value = $this->attributeValue;
        if ($value['size'] > $param * 1024) {
            if (is_null($this->attributeMessage)) {
                $error = "{$this->fileTypeDesc}不能大于%sKB.";
            } else {
                $error = "{$this->attributeLabel}{$this->attributeMessage}";
            }
            $this->errors[] = sprintf($error, number_format($param));
            return false;
        }
        return true;
    }

}

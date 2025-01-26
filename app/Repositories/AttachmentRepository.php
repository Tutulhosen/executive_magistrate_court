<?php

namespace App\Repositories;

use App\Appeal;

use App\Models\EmAttachment;
use App\Models\CauseList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class AttachmentRepository
{
    public static function appStoreAttachment($appName, $appealId, $causeListId, $captions, $request = null)
    {
        $image_name = $request->file_name['name'];

        $image = array(".jpg", ".jpeg", ".gif", ".png", ".bmp");
        $document = array(".doc", ".docx");
        $pdf = array(".pdf");
        $excel = array(".xlsx", ".xlsm", ".xltx", ".xltm");
        $text = array(".txt");
        $i = 0;
        $log_file_data = [];
        // $test = [];
        // ["file_name"]['name']
        foreach ($image_name as $key => $file) {

            $fileCategory = $captions[$i];


            $base364mage = substr($file, strpos($file, ',') + 1);

            //   $extension = explode('/', explode(';',$file)[0])[1];
            $image_data = base64_decode($base364mage);
            $fileextenxionget = (finfo_buffer(finfo_open(), $image_data, FILEINFO_MIME_TYPE));

            //   $fileName=$fileCategory.$appealId.$extension;
            if ($file != "" && $fileCategory != null) {
                $fileName = strtolower($image_data);

                if ($fileextenxionget === 'application/pdf') {
                    $extension  = "pdf";
                }
                if ($fileextenxionget === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    $extension = "docx";
                }

                $fileExtension = '.' . $extension;
                $fileContentType = "";
                // if (in_array($fileExtension, $image)) {
                //     $fileContentType = 'IMAGE';
                // }
                if (in_array($fileExtension, $document)) {
                    $fileContentType = 'DOCUMENT';
                }
                if (in_array($fileExtension, $pdf)) {
                    $fileContentType = 'PDF';
                }
                // if (in_array($fileExtension, $excel)) {
                //     $fileContentType = 'EXCEL';
                // }
                // if (in_array($fileExtension, $text)) {
                //     $fileContentType = 'TEXT';
                // }

                $fileName = self::getGUID() . $fileExtension;
                if ($fileContentType != "") {
                    $appealYear = 'APPEAL - ' . date('Y');
                    $appealID = 'AppealID - ' . $appealId;
                    $causeListID = 'CauseListID - ' . $causeListId;

                    $attachmentUrl = config('app.attachmentUrl');

                    $filePath = $attachmentUrl . $appName . '/' . $appealYear  . '/' . $appealID . '/' . $causeListID . '/';
                    // dd($filePath);
                    if (!is_dir($filePath)) {
                        mkdir($filePath, 0777, true);
                    }
                    $attachment = new EmAttachment();
                    $attachment->appeal_id = $appealId;
                    $attachment->cause_list_id = $causeListId;
                    $attachment->file_type = $fileContentType;
                    $attachment->file_category = $fileCategory;
                    $attachment->file_name = $fileName;
                    $attachment->file_path = $appName . '/' . $appealYear . '/' . $appealID . '/' . $causeListID . '/';
                    $attachment->file_submission_date = date('Y-m-d H:i:s');
                    $attachment->created_at = date('Y-m-d H:i:s');
                    // $attachment->created_by = Session::get('userInfo')->username;
                    $attachment->created_by = Auth::user()->username;
                    $attachment->updated_at = date('Y-m-d H:i:s');
                    // $attachment->updated_by = Session::get('userInfo')->username;
                    $attachment->updated_by = Auth::user()->username;
                    // dd($attachment);
                    $attachment->save();
                    // $test[$key] = $attachment;
                    // move_uploaded_file($image_data, $filePath . $fileName);

                    file_put_contents($filePath . $fileName, $image_data);
                    $file_in_log = [

                        'file_category' => $fileCategory,
                        'file_name' => $fileName,
                        'file_path' => $appName . '/' . $appealYear . '/' . $appealID . '/' . $causeListID . '/'
                    ];
                }
                array_push($log_file_data, $file_in_log);
            }
            $i++;
        }
        // dd($test);
        return json_encode($log_file_data);
    }
    public static function storeAttachment($appName, $appealId, $causeListId, $captions)
    {
        $image = array(".jpg", ".jpeg", ".gif", ".png", ".bmp");
        $document = array(".doc", ".docx");
        $pdf = array(".pdf");
        $excel = array(".xlsx", ".xlsm", ".xltx", ".xltm");
        $text = array(".txt");
        $i = 0;
        $log_file_data = [];
        // $test = [];
        // ["file_name"]['name']
        foreach ($_FILES['file_name']["name"] as $key => $file) {
            $tmp_name = $_FILES['file_name']["tmp_name"][$key];
            $fileName = $_FILES['file_name']["name"][$key];
            $fileCategory = $captions[$i];
            // dd($tmp_name.$fileName.$fileCategory);

            if ($fileName != "" && $fileCategory != null) {
                $fileName = strtolower($fileName);
                $fileExtension = '.' . pathinfo($fileName, PATHINFO_EXTENSION);

                $fileContentType = "";
                if (in_array($fileExtension, $image)) {
                    $fileContentType = 'IMAGE';
                }
                if (in_array($fileExtension, $document)) {
                    $fileContentType = 'DOCUMENT';
                }
                if (in_array($fileExtension, $pdf)) {
                    $fileContentType = 'PDF';
                }
                if (in_array($fileExtension, $excel)) {
                    $fileContentType = 'EXCEL';
                }
                if (in_array($fileExtension, $text)) {
                    $fileContentType = 'TEXT';
                }

                $fileName = self::getGUID() . $fileExtension;
                if ($fileContentType != "") {
                    $appealYear = 'APPEAL - ' . date('Y');
                    $appealID = 'AppealID - ' . $appealId;
                    $causeListID = 'CauseListID - ' . $causeListId;

                    $attachmentUrl = config('app.attachmentUrl');

                    $filePath = $attachmentUrl . $appName . '/' . $appealYear  . '/' . $appealID . '/' . $causeListID . '/';
                    // dd($filePath);
                    if (!is_dir($filePath)) {
                        mkdir($filePath, 0777, true);
                    }
                    $attachment = new EmAttachment();
                    $attachment->appeal_id = $appealId;
                    $attachment->cause_list_id = $causeListId;
                    $attachment->file_type = $fileContentType;
                    $attachment->file_category = $fileCategory;
                    $attachment->file_name = $fileName;
                    $attachment->file_path = $appName . '/' . $appealYear . '/' . $appealID . '/' . $causeListID . '/';
                    $attachment->file_submission_date = date('Y-m-d H:i:s');
                    $attachment->created_at = date('Y-m-d H:i:s');
                    // $attachment->created_by = Session::get('userInfo')->username;
                    $attachment->created_by = globalUserInfo()->username;
                    $attachment->updated_at = date('Y-m-d H:i:s');
                    // $attachment->updated_by = Session::get('userInfo')->username;
                    $attachment->updated_by = globalUserInfo()->username;
                    // dd($attachment);
                    $attachment->save();
                    // $test[$key] = $attachment;
                    move_uploaded_file($tmp_name, $filePath . $fileName);
                    $file_in_log = [

                        'file_category' => $fileCategory,
                        'file_name' => $fileName,
                        'file_path' => $appName . '/' . $appealYear . '/' . $appealID . '/' . $causeListID . '/'
                    ];
                }
                array_push($log_file_data, $file_in_log);
            }
            $i++;
        }
        // dd($test);
        return json_encode($log_file_data);
    }

    public static function storeReqAttachment($attach_file, $appealId, $user)
    {
        $attach_file = json_decode($attach_file);
        $log_file_data = [];
        $i = 0;
        foreach ($attach_file as $key => $file) {
            $base364mage = substr($file->tmp_base_64, strpos($file->tmp_base_64, ',') + 1);
            $extension = explode('/', explode(';', $file->tmp_base_64)[0])[1];
            $image_data = base64_decode($base364mage);

            $tmp_name = $file->tmp_name;

            $fileName = $file->file_name;
            $fileCategory = $file->file_category;
            //dd($tmp_name.$fileName.$fileCategory);

            if ($fileName != "" && $fileCategory != null) {

                $fileContentType = $file->fileContentType;

                if ($fileContentType != "") {
                    $fileName = self::getGUID() . '.' . $extension;

                    $appealYear = 'APPEAL - ' . date('Y');
                    $appealID = 'AppealID - ' . $appealId;
                    $causeListID = 'CauseListID - ' . $file->causeListID;
                    $attachmentUrl = config('app.attachmentUrl');
                    $path = $file->appName . '/' . $appealYear .  '/' . $appealId . '/' . $file->causeListID . '/';
                    $filePath = $attachmentUrl . $file->appName . '/' . $appealYear  . '/' . $appealID . '/' . $causeListID . '/';
                    // dd($filePath . $fileName);
                    if (!is_dir($filePath)) {
                        mkdir($filePath, 0777, true);
                    }
                    $is_store = DB::table('em_attachments')->insert([
                        'appeal_id' => $appealId,
                        'cause_list_id' => date('Y'),
                        'file_type' => $fileContentType,
                        'file_category' => $fileCategory,
                        'file_name' => $fileName,
                        'file_path' => $path,
                        'file_submission_date' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user['username'],
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => $user['username'],
                    ]);
                    if ($is_store) {
                        file_put_contents($filePath . $fileName, $image_data);
                        $file_in_log = [

                            'file_category' => $fileCategory,
                            'file_name' => $fileName,
                            'file_path' => $path
                        ];
                    } else {
                        $file_in_log = null;
                    }
                }
                array_push($log_file_data, $file_in_log);
            }
            $i++;
        }
        // dd($log_file_data);
        return json_encode($log_file_data);
    }

    public static function getAttachmentListByAppealId($appealId)
    {
        $attachmentList = DB::connection('mysql')
            ->table('em_attachments')
            ->leftjoin('em_cause_lists', 'em_cause_lists.id', '=', 'em_attachments.cause_list_id')
            ->where('em_attachments.appeal_id', $appealId)
            ->get();
        return $attachmentList;
    }

    public static function getAttachmentListByAppealIdAndCauseListId($appealId, $causeListId)
    {
        // $attachmentList=DB::connection('appeal')
        $attachmentList = DB::connection('mysql')
            ->table('em_cause_lists')
            ->join('em_attachments', 'em_cause_lists.id', '=', 'em_attachments.cause_list_id')
            ->where('em_attachments.appeal_id', $appealId)
            ->where('em_cause_lists.id', $causeListId)
            ->get();
        return $attachmentList;
    }

    public static function getAttachmentListByPaymentId($paymentId)
    {
        $attachmentList = DB::connection('appeal')
            ->table('attachments')
            ->where('attachments.payment_id', $paymentId)
            ->get();
        return $attachmentList;
    }

    public static function deleteFileByFileID($fileID)
    {

        $attachment = EmAttachment::find($fileID);
        $fileName = $attachment->file_name;

        $attachmentUrl = config('app.attachmentUrl');
        $filePath = $attachmentUrl . $attachment->file_path;
        if ($attachment !== false) {
            if ($attachment->delete() === false) {

                $messages = $attachment->getMessages();

                foreach ($messages as $message) {
                    echo $message, "\n";
                }
            } else {
                unlink($filePath . $fileName);
            }
        }
    }

    public static function deleteAppealFile($fileID, $appealID)
    {

        $attachment = EmAttachment::find($fileID);
        $fileName = $attachment->file_name;
        LogManagementRepository::Appealfiledelete($attachment, $appealID);
        $attachmentUrl = config('app.attachmentUrl');
        $filePath = $attachmentUrl . $attachment->file_path;
        if ($attachment !== false) {
            if ($attachment->delete() === false) {

                $messages = $attachment->getMessages();

                foreach ($messages as $message) {
                    echo $message, "\n";
                }
            } else {
                unlink($filePath . $fileName);
            }
        }
    }

    public static function getGUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public static function storeAttachmentOnPayment($appName, $appealId, $paymentId, $captions)
    {
        $image = array(".jpg", ".jpeg", ".gif", ".png", ".bmp");
        $document = array(".doc", ".docx");
        $pdf = array(".pdf");
        $excel = array(".xlsx", ".xlsm", ".xltx", ".xltm");
        $text = array(".txt");
        $i = 0;

        foreach ($_FILES["files"]["name"] as $key => $file) {
            $tmp_name = $_FILES["files"]["tmp_name"][$key]['someName'];
            $fileName = $_FILES["files"]["name"][$key]['someName'];
            $fileCategory = $captions[$i]['someCaption'];

            if ($fileName != "" && $fileCategory != null) {
                $fileName = strtolower($fileName);
                $fileExtension = '.' . pathinfo($fileName, PATHINFO_EXTENSION);

                $fileContentType = "";
                if (in_array($fileExtension, $image)) {
                    $fileContentType = 'IMAGE';
                }
                if (in_array($fileExtension, $document)) {
                    $fileContentType = 'DOCUMENT';
                }
                if (in_array($fileExtension, $pdf)) {
                    $fileContentType = 'PDF';
                }
                if (in_array($fileExtension, $excel)) {
                    $fileContentType = 'EXCEL';
                }
                if (in_array($fileExtension, $text)) {
                    $fileContentType = 'TEXT';
                }

                $fileName = self::getGUID() . $fileExtension;
                if ($fileContentType != "") {
                    $appealYear = 'APPEAL - ' . date('Y');
                    $appealID = 'AppealID - ' . $appealId;
                    $causeListID = 'PaymentID - ' . $paymentId;

                    $attachmentUrl = config('app.attachmentUrl');

                    $filePath = $attachmentUrl . $appName . '/' . $appealYear  . '/' . $appealID . '/' . $causeListID . '/';
                    if (!is_dir($filePath)) {
                        mkdir($filePath, 0777, true);
                    }
                    $attachment = new EmAttachment();
                    $attachment->appeal_id = $appealId;
                    $attachment->payment_id = $paymentId;
                    $attachment->file_type = $fileContentType;
                    $attachment->file_category = $fileCategory;
                    $attachment->file_name = $fileName;
                    $attachment->file_path = $appName . '/' . $appealYear . '/' . $appealID . '/' . $causeListID . '/';
                    $attachment->file_submission_date = date('Y-m-d H:i:s');
                    $attachment->created_at = date('Y-m-d H:i:s');
                    $attachment->created_by = globalUserInfo()->username;
                    $attachment->updated_at = date('Y-m-d H:i:s');
                    $attachment->updated_by = globalUserInfo()->username;
                    $attachment->save();
                    move_uploaded_file($tmp_name, $filePath . $fileName);
                }
            }
            $i++;
        }
    }

    public static function storeInvestirationAttachment($appName, $appealId, $captions, $captions_others_investigation_report)
    {
        $image = array(".jpg", ".jpeg", ".gif", ".png", ".bmp");
        $document = array(".doc", ".docx");
        $pdf = array(".pdf");
        $excel = array(".xlsx", ".xlsm", ".xltx", ".xltm");
        $text = array(".txt");
        $i = 0;
        // $test = [];
        // ["file_name"]['name']
        $log_file_data = [];
        foreach ($_FILES['file_name']["name"] as $key => $file) {
            $tmp_name = $_FILES['file_name']["tmp_name"][$key];
            $fileName = $_FILES['file_name']["name"][$key];
            $fileCategory = $captions_others_investigation_report . ' ' . $captions[$i];

            // dd($tmp_name.$fileName.$fileCategory);

            if ($fileName != "" && $fileCategory != null) {
                $fileName = strtolower($fileName);
                $fileExtension = '.' . pathinfo($fileName, PATHINFO_EXTENSION);

                $fileContentType = "";
                if (in_array($fileExtension, $image)) {
                    $fileContentType = 'IMAGE';
                }
                if (in_array($fileExtension, $document)) {
                    $fileContentType = 'DOCUMENT';
                }
                if (in_array($fileExtension, $pdf)) {
                    $fileContentType = 'PDF';
                }
                if (in_array($fileExtension, $excel)) {
                    $fileContentType = 'EXCEL';
                }
                if (in_array($fileExtension, $text)) {
                    $fileContentType = 'TEXT';
                }

                $fileName = self::getGUID() . $fileExtension;
                if ($fileContentType != "") {
                    $appealYear = 'APPEAL - ' . date('Y');
                    $appealID = 'AppealID - ' . $appealId;


                    $attachmentUrl = config('app.attachmentUrl');

                    $filePath = $attachmentUrl . $appName . '/' . $appealYear  . '/' . $appealID . '/';
                    // dd($filePath);
                    if (!is_dir($filePath)) {
                        mkdir($filePath, 0777, true);
                    }
                    // $attachment = new EmAttachment();
                    // $attachment->appeal_id = $appealId;
                    // $attachment->file_type = $fileContentType;
                    // $attachment->file_category = $fileCategory;
                    // $attachment->file_name = $fileName;
                    // $attachment->file_path = $appName . '/' . $appealYear . '/' .$appealID. '/';
                    // $attachment->file_submission_date = date('Y-m-d H:i:s');
                    // $attachment->created_at = date('Y-m-d H:i:s');
                    // // $attachment->created_by = Session::get('userInfo')->username;
                    // // $attachment->created_by = Auth::user()->username;
                    // $attachment->updated_at = date('Y-m-d H:i:s');
                    // // $attachment->updated_by = Session::get('userInfo')->username;
                    // // $attachment->updated_by = Auth::user()->username;
                    // // dd($attachment);
                    // $attachment->save();
                    // $test[$key] = $attachment;
                    move_uploaded_file($tmp_name, $filePath . $fileName);
                    $file_in_log = [

                        // 'file_id'=>$attachment->id,
                        'file_category' => $fileCategory,
                        'file_name' => $fileName,
                        'file_path' => $appName . '/' . $appealYear . '/' . $appealID . '/'
                    ];
                }
                array_push($log_file_data, $file_in_log);
            }
            $i++;
        }
        // dd($test);

        return json_encode($log_file_data);
    }

    public static function base64_decode_if_needed($str)
    {
        // Check if the string is valid base64 encoded
        if (base64_decode($str, true) !== false) {
            // Decode the base64 string
            return base64_decode($str);
        } else {
            // Return the original string if it's not base64 encoded
            return $str;
        }
    }

    public static function storeInvestirationMainAttachment($appName, $appealId, $captions_main_investigation_report, $fileData)
    {

        // return [$appName];
        $imageExtensions = array(".jpg", ".jpeg", ".gif", ".png", ".bmp", '.pdf');

        foreach ($fileData as $key => $file) {
            // return $file;
            // Extracting file details
            $tmp_name = $file['tmp_name'];
            $fileName = $file['file_name'];
            $fileCategory = 'x';
            $base364mage = substr($file->tmp_base_64, strpos($file->tmp_base_64, ',') + 1);
            $extension = explode('/', explode(';', $file->tmp_base_64)[0])[1];
            $image_data = /* urlencode( */base64_decode($base364mage)/* ) */;;
            // $image_data = base64_decode($base364mage);
            return [$image_data];
        }

        // Check if file details are valid
        if ($fileName != "" && $fileCategory != null) {
            // Lowercase the file name and get its extension
            $fileName = strtolower($fileName);
            $fileExtension = '.' . pathinfo($fileName, PATHINFO_EXTENSION);

            // Check if the file extension is supported
            if (in_array($fileExtension, $imageExtensions)) {
                // Generate a unique file name
                // return ['mes' =>  $imageExtensions];
                $fileName = self::getGUID() . $fileExtension;

                // Define file paths
                $appealYear = 'APPEAL - ' . date('Y');
                $appealID = 'AppealID - ' . $appealId;
                $attachmentUrl = config('app.attachmentUrl');
                $filePath = $attachmentUrl . $appName . '/' . $appealYear  . '/' . $appealID . '/';

                // Create directory if it doesn't exist
                if (!is_dir($filePath)) {
                    mkdir($filePath, 0777, true);
                }

                // return [file_put_contents($filePath . $fileName, $tmp_name)];

                // Move uploaded file to destination
                if (file_put_contents($filePath . $fileName, $tmp_name)) {
                    // Create file log data
                    $file_in_log = [
                        'file_category' => $captions_main_investigation_report,
                        'file_name' => $fileName,
                        'file_path' => $appName . '/' . $appealYear . '/' . $appealID . '/'
                    ];

                    return json_encode([$file_in_log]);
                } else {
                    // Error occurred while moving the file
                    return json_encode([]);
                }
            } else {
                // Unsupported file extension
                return json_encode([]);
            }
        } else {
            // Invalid file details
            return json_encode([]);
        }
    }

    public static function storeInvestirationMainAttachment_new($appName, $appealId,$captions_main_investigation_report, $investigation_attachment_main)
    {
        // return $investigation_attachment_main;
        $log_file_data=[];
        $i = 0;
        foreach (json_decode($investigation_attachment_main) as $key => $file) {
           
            $base364mage = substr($file->tmp_base_64, strpos($file->tmp_base_64, ',') + 1);
            $extension = explode('/', explode(';', $file->tmp_base_64)[0])[1];
            $image_data = base64_decode($base364mage);
      
            $tmp_name = $file->tmp_name;

            $fileName = $file->file_name;
            $fileCategory = $file->file_category;

            if ($fileName != "" && $fileCategory != null) {

                $fileContentType = $file->fileContentType;

                if ($fileContentType != "") {
                    $fileName = self::getGUID() . '.' . $extension;

                    $appealYear = 'APPEAL - ' . date('Y');
                    $appealID = 'AppealID - ' . $appealId;
                    $causeListID = 'CauseListID - ' . $file->causeListID;
                    $attachmentUrl = config('app.attachmentUrl');
                    $path = $file->appName . '/' . $appealYear .  '/' . $appealID . '/';
                    $filePath = $attachmentUrl . $file->appName . '/' . $appealYear  . '/' . $appealID .  '/';
                    // return [$filePath];
                    // dd($filePath . $fileName);
                    if (!is_dir($filePath)) {
                        mkdir($filePath, 0777, true);
                    }
                    
                    if ($investigation_attachment_main) {
                        file_put_contents($filePath . $fileName, $image_data);
                        $file_in_log = [

                            'file_category' => $fileCategory,
                            'file_name' => $fileName,
                            'file_path' => $path
                        ];
                    } else {
                        $file_in_log = null;
                    }
                }
                array_push($log_file_data, $file_in_log);
            }
            $i++;
        }
        // dd($test);

        return json_encode($log_file_data);
    }


    public static function storeInvestirationCourtFree($appName, $appealId, $captions_main_investigation_report)
    {
        $image = array(".jpg", ".jpeg", ".gif", ".png", ".bmp");
        $document = array(".doc", ".docx");
        $pdf = array(".pdf");
        $excel = array(".xlsx", ".xlsm", ".xltx", ".xltm");
        $text = array(".txt");


        $log_file_data = [];

        $tmp_name = $_FILES['court_fee_file']["tmp_name"];
        $fileName = $_FILES['court_fee_file']["name"];
        $fileCategory = 'x';

        if ($fileName != "" && $fileCategory != null) {
            $fileName = strtolower($fileName);
            $fileExtension = '.' . pathinfo($fileName, PATHINFO_EXTENSION);

            $fileContentType = "";
            if (in_array($fileExtension, $image)) {
                $fileContentType = 'IMAGE';
            }
            if (in_array($fileExtension, $document)) {
                $fileContentType = 'DOCUMENT';
            }
            if (in_array($fileExtension, $pdf)) {
                $fileContentType = 'PDF';
            }
            if (in_array($fileExtension, $excel)) {
                $fileContentType = 'EXCEL';
            }
            if (in_array($fileExtension, $text)) {
                $fileContentType = 'TEXT';
            }

            $fileName = self::getGUID() . $fileExtension;
            if ($fileContentType != "") {
                $appealYear = 'APPEAL - ' . date('Y');
                $appealID = 'AppealID - ' . $appealId;


                $attachmentUrl = config('app.attachmentUrl');

                $filePath = $attachmentUrl . $appName . '/' . $appealYear  . '/' . $appealID . '/';
                // dd($filePath);
                if (!is_dir($filePath)) {
                    mkdir($filePath, 0777, true);
                }
                move_uploaded_file($tmp_name, $filePath . $fileName);
                $file_in_log = [
                    'file_category' => $captions_main_investigation_report,
                    'file_name' => $fileName,
                    'file_path' => $appName . '/' . $appealYear . '/' . $appealID . '/'
                ];
            }
            array_push($log_file_data, $file_in_log);
        }
        // dd($log_file_data);
        if (!empty($log_file_data)) {

            return json_encode($log_file_data);
        } else {
            return null;
        }
    }
}
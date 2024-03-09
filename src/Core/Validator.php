<?php

namespace Src\Core;

class Validator
{

    public static function SendResult($data, $successMesg = "", $errorMsg = "")
    {
        if (!$data) return ["success" => false, "message" => $errorMsg];
        return ["success" => true, "message" => $successMesg];
    }

    public static function genCode($length = 6)
    {
        $digits = $length;
        return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }

    public static function IndexNumber($input): mixed
    {
        if (empty($input)) die(json_encode(array("success" => false, "message" => "Index number required!")));
        if (strlen($input) !== 10) die(json_encode(array("success" => false, "message" => "Invalid index number!")));
        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/[A-Z0-9]/', $user_input);
        if ($validated_input) return $input;
        die(json_encode(array("success" => false, "message" => "Invalid index number!")));
    }

    public static function Email($input): mixed
    {
        if (empty($input)) die(json_encode(array("success" => false, "message" => "Email address is required!")));
        $user_email = htmlentities(htmlspecialchars($input));
        $sanitized_email = filter_var($user_email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($sanitized_email, FILTER_VALIDATE_EMAIL))
            die(json_encode(array("success" => false, "message" => "Invalid email address!")));
        return $input;
    }

    public static function Password($input)
    {
        if (empty($input)) die(json_encode(array("success" => false, "message" => "Password required!")));
        if (strlen($input) < 8 || strlen($input) > 16) die(json_encode(array("success" => false, "message" => "Invalid password length!")));
        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/[A-Za-z0-9()+@#.-_=$&!`]/', $user_input);
        if ($validated_input) return $input;
        die(json_encode(array("success" => false, "message" => "Invalid password!")));
    }

    public static function Password2($password): mixed
    {
        $user_input = htmlentities(htmlspecialchars($password));
        // Password must be at least 8 characters long
        if (strlen($user_input) < 8 || strlen($user_input) > 16) {
            die(json_encode(array("success" => false, "message" => "Password must be at least 8 or most 16 characters long!")));
        }

        // Password must have at least one uppercase letter
        if (!preg_match('/[A-Z]/', $user_input)) {
            die(json_encode(array("success" => false, "message" => "Password must have at least one uppercase letter!")));
        }

        // Password must have at least one lowercase letter
        if (!preg_match('/[a-z]/', $user_input)) {
            die(json_encode(array("success" => false, "message" => "Password must have at least one lowercase letter!")));
        }

        // Password must have at least one digit
        if (!preg_match('/\d/', $user_input)) {
            die(json_encode(array("success" => false, "message" => "Password must have at least one digit!")));
        }

        // Password must have at least one special character
        if (!preg_match('/[\',@,$,#,!,*,+,\-.,,\,]/', $user_input)) {
            die(json_encode(array("success" => false, "message" => "Password must have at least one special character!")));
        }

        return $password;
    }


    public static function InputTextNumber($input)
    {
        if (empty($input)) die(json_encode(array("success" => false, "message" => "Input required")));
        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/[A-Za-z0-9]/', $user_input);
        if ($validated_input) return $user_input;
        die(json_encode(array("success" => false, "message" => "invalid")));
    }

    public static function InputTextNumberForArray(array $input)
    {
        if (empty($input)) die(json_encode(array("success" => false, "message" => "Input required")));
        for ($i = 0; $i < count($input); $i++) {
            $user_input = htmlentities(htmlspecialchars($input[$i]));
            $validated_input = (bool) preg_match('/[A-Za-z0-9]/', $user_input);
            if (!$validated_input) die(json_encode(array("success" => false, "message" => "invalid input received!")));
        }
        return $input;
    }

    public static function InputTextOnly($input)
    {
        if (empty($input)) die(json_encode(array("success" => false, "message" => "Input required")));
        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/[A-Za-z]/', $user_input);
        if ($validated_input) return $input;
        die(json_encode(array("success" => false, "message" => "invalid")));
    }

    public static function NumberOnly($input)
    {
        if (empty($input)) die(json_encode(array("success" => false, "message" => "Input required!")));
        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/[0-9]/', $user_input);
        if ($validated_input) return  array("success" => true, "message" => $user_input);
        die(json_encode(array("success" => false, "message" => "Invalid input!")));
    }

    public static function Date($date)
    {
        if (strtotime($date) === false) die(json_encode(array("success" => false, "message" => "Invalid date!")));
        list($year, $month, $day) = explode('-', $date);
        if (checkdate($month, $day, $year)) die(json_encode(array("success" => true, "message" => $date)));
    }

    public static function Image($files)
    {
        if (!isset($files['file']['error']) || !empty($files["pics"]["name"])) {
            $allowedFileType = ['image/jpeg', 'image/png', 'image/jpg'];
            for ($i = 0; $i < count($files["pics"]["name"]); $i++) {
                $check = getimagesize($files["pics"]["tmp_name"][$i]);
                if ($check !== false && in_array($files["pics"]["type"][$i], $allowedFileType))
                    die(json_encode(array("success" => true, "message" => $files)));
            }
        }
        die(json_encode(array("success" => false, "message" => "Invalid file uploaded!")));
    }

    public static function YearData($input)
    {
        if (empty($input)) die(json_encode(array("success" => false, "message" => "Input required")));
        //if ($input < 1990 || $input > 2022) die(json_encode(array("success" => false, "message" => "invalid");
        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/[0-9]/', $user_input);
        if ($validated_input) return $input;
        die(json_encode(array("success" => false, "message" => "Input invalid")));
    }

    public static function Grade($input)
    {
        if (empty($input) || strtoupper($input) == "GRADE") die(json_encode(array("success" => false, "message" => "Input required")));
        if (strlen($input) < 1 || strlen($input) > 2) die(json_encode(array("success" => false, "message" => "Input invalid")));
        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/[A-Za-z]/', $user_input);
        if ($validated_input) return $input;
        die(json_encode(array("success" => false, "message" => "Input invalid")));
    }
}

<?php
namespace GdValidation;
/**
 * gufii framework (https://blog.gufii.net)
 * Copyright 2013 gufii -Just right for you-
 * Licensed under The MIT License
 *
 * VERSION 3.0 composerに対応 Class名変更
 * VERSION 2.0
 * 値の検証クラス
 */

class GdValidation
{
    const T_REQUIRE      = "REQUIRE";
    const T_EMPTY        = "EMPTY";
    const T_MAX          = "MAX";
    const T_MIN          = "MIN";
    const T_MAIL         = "MAIL";
    const T_NUMBER       = "NUMBER";
    const T_PATTERN      = "PATTERN";
    const T_CONFIRM      = "CONFIRM";
    const T_ZENKAKU_KANA = "ZENKAKU_KANA";
    const T_URL = "URL";

    protected $checkType    = array(
              self::T_EMPTY
            , self::T_REQUIRE
            , self::T_MAX
            , self::T_MIN
            , self::T_MAIL
            , self::T_NUMBER
            , self::T_PATTERN
            , self::T_CONFIRM
            , self::T_ZENKAKU_KANA
            , self::T_URL
    );
    protected $checkColumns = array();
    protected $params       = array();
    protected $keyName      = array();
	protected $error        = array();

    protected $MAIL_PATTERN = '/^([a-z0-9_]|\-|\.|\+|\/|\!|\#|\$|\%|\&)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,6}$/i';
    protected $URL_PATTERN  = '/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/';
    protected $charset      = "UTF-8";

    protected $EMPTY_MSG         = "%sを入力してください。";
    protected $MAX_MSG           = "%sは%d文字以内で入力してください。";
    protected $MIN_MSG           = "%sは%d文字以上で入力してください。";
    protected $MAIL_MSG          = "%sは有効なメールアドレスではありません。";
    protected $NUMBER_MSG        = "%sは数値を入力してください。";
    protected $PATTERN_MSG       = "%sは不正な値です。";
    protected $CONFIRM_MSG       = "%sと、%sが一致していません。";
    protected $ZENKAKU_KANA_MSG  = "%sは全角カタカナで入力してください。";
    protected $URL_MSG           = "%sは有効なURLではありません。";


    /*
     * バリデーション定義を追加
     *
     * @param $coloumName 値の名前 (string|array)
     * @param $restraction 検証タイプ (EMPTY|MAX|MIN|MAIL|NUMBER|PATTERN|CONFIRM)
     * @param $option オプション MAX,MINの場合は文字数 PATTERNの場合は正規表現文字列 CONFIRMの場合は検証するもうひとつの値の名前
     */
    public function add($columName, $restriction, $option = null)
    {
        if (!in_array(strtoupper($restriction), $this->checkType))
            throw new GdValidationException(GdValidationException::CHECK_TYPE_ERROR);
        if (gettype($columName) == "array") {
            foreach($columName as $name) {
                $this->checkColumns[] = array($name, $restriction, $option);
            }
        } else {
            $this->checkColumns[] = array($columName, $restriction, $option);
        }
    }

    /*
     * 値を設定
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /*
     * 値の追加・変更
     */
    public function addParams($key, $value)
    {
        $this->params[$key] = $value;
    }

    /*
     * 値の名前の設定
     */
    public function setName($keyNames)
    {
        $this->keyName = $keyNames;
    }

    /*
     * 値の名前の追加・変更
     */
    public function addName($key, $value)
    {
        $this->keyName[$key] = $value;
    }

    /*
     * 入力検証の実行
     */
    public function valid()
    {
        if (empty($this->params))
                throw  new GdValidationException(GdValidationException::NO_PARAMS_ERROR);
        $errorMsg = array();
        $defaultCharset = mb_regex_encoding();
        mb_regex_encoding($this->charset);
        foreach ($this->checkColumns as $checkColumn) {
            $paramName = isset($this->keyName[$checkColumn[0]]) ? $this->keyName[$checkColumn[0]] : $checkColumn[0];
            switch (strtoupper($checkColumn[1])) {
                case (self::T_EMPTY) :
                case (self::T_REQUIRE) :
                    if ($this->params[$checkColumn[0]] === null || $this->params[$checkColumn[0]] === ""){
                        if (!isset($errorMsg[$checkColumn[0]])) $errorMsg[$checkColumn[0]] = array();
						$errorMsg[$checkColumn[0]][] = sprintf($this->EMPTY_MSG, $paramName);
					}
                    break;
                case (self::T_MAX) :
                    if ($this->params[$checkColumn[0]] != "" && mb_strlen($this->params[$checkColumn[0]], $this->charset) > $checkColumn[2]){
						if (!isset($errorMsg[$checkColumn[0]])) $errorMsg[$checkColumn[0]] = array();
						$errorMsg[$checkColumn[0]][] = sprintf($this->MAX_MSG, $paramName, $checkColumn[2]);
					}
                    break;
                case (self::T_MIN) :
                    if ($this->params[$checkColumn[0]] != "" && mb_strlen($this->params[$checkColumn[0]], $this->charset) < $checkColumn[2]){
						if (!isset($errorMsg[$checkColumn[0]])) $errorMsg[$checkColumn[0]] = array();
                        $errorMsg[$checkColumn[0]][] = sprintf($this->MIN_MSG, $paramName, $checkColumn[2]);
					}
                    break;
                case (self::T_MAIL) :
                    if ($this->params[$checkColumn[0]] != "" && !preg_match($this->MAIL_PATTERN, $this->params[$checkColumn[0]])){
						if (!isset($errorMsg[$checkColumn[0]])) $errorMsg[$checkColumn[0]] = array();
                        $errorMsg[$checkColumn[0]][] = sprintf($this->MAIL_MSG, $paramName);
					}
                    break;
                case (self::T_NUMBER) :
                    if ($this->params[$checkColumn[0]] && !is_numeric($this->params[$checkColumn[0]])){
						if (!isset($errorMsg[$checkColumn[0]])) $errorMsg[$checkColumn[0]] = array();
                        $errorMsg[$checkColumn[0]][] = sprintf($this->NUMBER_MSG, $paramName);
					}
                    break;
                case (self::T_PATTERN) :
                    if ($this->params[$checkColumn[0]] != "" && !preg_match($checkColumn[2], $this->params[$checkColumn[0]])){
						if (!isset($errorMsg[$checkColumn[0]])) $errorMsg[$checkColumn[0]] = array();
                        $errorMsg[$checkColumn[0]][] = sprintf($this->PATTERN_MSG, $paramName);
					}
                    break;
                case (self::T_CONFIRM) :
                    $confirmName = $this->keyName[$checkColumn[2]] ? $this->keyName[$checkColumn[2]] : $checkColumn[2];
                    if ($this->params[$checkColumn[2]] != $this->params[$checkColumn[0]]){
						if (!isset($errorMsg[$checkColumn[0]])) $errorMsg[$checkColumn[0]] = array();
                        $errorMsg[$checkColumn[0]][] = sprintf($this->CONFIRM_MSG, $paramName, $confirmName);
					}
                    break;
                case (self::T_ZENKAKU_KANA) :
                    if ($this->params[$checkColumn[0]] != "" && !mb_ereg("^[ァ-ヶ 　ー]+$", $this->params[$checkColumn[0]])){
						if (!isset($errorMsg[$checkColumn[0]])) $errorMsg[$checkColumn[0]] = array();
                        $errorMsg[$checkColumn[0]][] = sprintf($this->ZENKAKU_KANA_MSG, $paramName);
					}
                    break;
                case (self::T_URL) :
                    if ($this->params[$checkColumn[0]] != "" && !preg_match($this->URL_PATTERN, $this->params[$checkColumn[0]])){
						if (!isset($errorMsg[$checkColumn[0]])) $errorMsg[$checkColumn[0]] = array();
                        $errorMsg[$checkColumn[0]][] = sprintf($this->URL_MSG, $paramName);
					}
                    break;
                default :
                    throw  new Exception();
                    break;
            }
        }
        mb_regex_encoding($defaultCharset);
        return $errorMsg;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
	
}
class GdValidationException extends Exception
{
    const CHECK_TYPE_ERROR = 'GdValidation::add error! You have to set $restriction by (EMPTY|MAX|MIN|MAIL|NUMBER|PATTERN).';
    const NO_PARAMS_ERROR  = 'GdValidation::valid error! Missing parameter.';
}

/**
 * 使い方は、こんな感じ
// インスタンス生成
$validObj = new GdValidation();

// 値をセット
$post = array("name" => "", "age" => "", "mail" => "info@gufii.net", "mail_confirm" => "info2@gufii.net");
$validObj->setParams($post);

// 値の追加・変更(同じキーがすでにあれば、変更)
$validObj->addParams("address", "福岡県福岡市博多区");

// 値の名前をセット 無ければ、セットした値のキーが名前になります。
$validObj->setName(array("name" => "名前", "age" => "年齢", "mail" => "メールアドレス", "mail_confirm" => "メールアドレス（確認用）"));

// 値の名前を追加・変更(同じキーがすでにあれば、変更)
$validObj->addName("address", "住所");

// 検証定義を設定
$validObj->add("name", "EMPTY");
$validObj->add("name", "MAX", 20);
$validObj->add("age", "EMPTY");
$validObj->add("age", "NUMBER");
$validObj->add("age", "MAX", 3);
$validObj->add("mail", "MAIL");
$validObj->add("mail", "MAX", 200);
$validObj->add("mail_confirm", "CONFIRM", "mail");

// 同じ定義であれば、配列で検証定義できます
$validObj->add(array("mail", "mail_confirm"), "EMPTY");

// 第2引数の検証タイプは小文字でもいけます
$validObj->add("name", "min", 2);

// 正規表現チェックも出来ます
$validObj->add("mail", "PATTERN", "/[a-z]*@[a-zA-Z0-9.]/");

// 検証実行
$errors = $validObj->valid();

var_dump($errors);
*/
?>
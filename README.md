# PHPのvalidation機能提供class

## 使い方
```
<?php
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

```

<?php

//作成した関数を呼び出し
require_once('php/common.php');

//動作モード
$mode = "edit";
if(isset($_POST['check'])){
  $mode = "check";
}elseif(isset($_POST['entry'])){
  $mode = "entry";
}elseif(isset($_POST['input'])){
  $mode = "input";
}

//editモード以外の場合　POSTされた値を変数に取得
if ($mode=='check' || $mode=='entry' || $mode=='input') {
  //入力内容を変数に取得
  $post=sanitize($_POST);
  $name=$post['name'];
  $sex=$post['sex'];
  $age=$post['age'];
  $area=$post['area'];
  $favorite=$post['favorite'];
  $impressions=$post['impressions'];
  $remarks=$post['remarks'];

  if ($mode=='check') {
    //複数選択されたcueを結合
    if (isset($cue) && isset($post['cue1'])) {
      $cue=$cue.'、'.$post['cue1'];
    }elseif (isset($post['cue1'])) {
      $cue=$post['cue1'];
    }
    if (isset($cue) && isset($post['cue2'])) {
      $cue=$cue.'、'.$post['cue2'];
    }elseif (isset($post['cue2'])) {
      $cue=$post['cue2'];
    }
    if (isset($cue) && isset($post['cue3'])) {
      $cue=$cue.'、'.$post['cue3'];
    }elseif (isset($post['cue3'])) {
      $cue=$post['cue3'];
    }
    if (isset($cue) && isset($post['cue4'])) {
      $cue=$cue.'、'.$post['cue4'];
    }elseif (isset($post['cue4'])) {
      $cue=$post['cue4'];
    }
  }else {
    $cue=$post['cue'];
  }

}

//entryモードの場合、DBへ書き込み
if ($mode=='entry') {
	try {

    //DBに接続
    $dsn='mysql:dbname=●●●;host=●●●;charset=utf8';
    $user='●●●';
    $password='●●●';
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    //新規レコード処理
    $sql='INSERT INTO worksheet(name,sex,age,area,cue,favorite,impressions,remarks) VALUES (?,?,?,?,?,?,?,?)';
    $stmt=$dbh->prepare($sql);
    $data[]=$name;
    $data[]=$sex;
    $data[]=$age;
    $data[]=$area;
    $data[]=$cue;
    $data[]=$favorite;
    $data[]=$impressions;
    $data[]=$remarks;
    $stmt->execute($data);

    // カレントの言語を日本語に設定する
    mb_language("ja");
    // 内部文字エンコードを設定する
    mb_internal_encoding("UTF-8");

    //メール送信
    $to = "●●●";//宛先
    $subject = "NUS3MEMOのアンケートに記載がありました（".date("Y/m/d",time()).")"; //題名
    $body = "\n\nお名前：".$name."\n年齢：".$age."\n性別：".$sex."\nきっかけ：".$cue."\n気になる記事：".$favorite."\n感想：".$impressions."\nhadaに一言：".$remarks; //本文
    $from = "●●●"; //差出人

    //メールの送信
    $send_mail = mb_send_mail($to, $subject, $body, $from);

    //メールの送信に問題ないかチェック
    if ($send_mail) {
    } else {
      echo "正常に登録できませんでした。";
    }

    $dbh=null;

	} catch (Exception $e) {
		echo "ごめんなさい。hadaの未熟さが原因で、エラーが発生しました";
	}

}

//inputモードの場合、選択されたcueを判別
if ($mode=='input') {
  if (substr_count($cue, 'Facebook')>0) {
    $cue1Check = 1;
  }
  if (substr_count($cue, 'Twitter')>0) {
    $cue2Check = 1;
  }
  if (substr_count($cue, '検索')>0) {
    $cue3Check = 1;
  }
  if (substr_count($cue, 'hadaのファン')>0) {
    $cue4Check = 1;
  }
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
  <title>nus3Memoアンケート</title>
  <!-- OGP -->
  <meta property="og:title" content="NUS3MEMOの感想をぜひ" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="http://nus3.moo.jp/nus3Sheet/form.php" />
  <meta property="og:image" content="http://nus3.moo.jp/nus3Sheet/img/OGP.png" />
  <meta property="og:site_name" content="NUS3MEMO" />
  <meta property="og:description" content="NUS3MEMOのアンケートフォームです" />
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:description" content="NUS3MEMOのアンケートフォームです" />
  <!-- //OGP -->
  <link rel="shortcut icon" href="img/favicon.png">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <!-- header -->
  <header class="header-fixed">
    <div class="row header">
      <div class="col-sm-8 mainTitle">
        <h1>NUS3 MEMOの感想</h1>
      </div>
      <div class="col-sm-4 backBtn">
        <a href="http://nus3.moo.jp/">HOMEに戻る</a>
      </div>
    </div>
  </header>
  <!-- //header -->

  <!-- contents -->
  <div class="contents">

    <?PHP if($mode=="edit"){ ?>
    <!-- editモード -->
    <div class="form">
      <form class="form_area" method="post" action="form.php">
        <p class="input_label">ペンネーム：</p>
        <input class="input_text" type="text" name="name">
        <p class="input_label">性別：</p>
        <input type="radio" name="sex" value="man" id="sex-01">
        <label for="sex-01" class="input_radio">男性</label>
        <input type="radio" name="sex" value="woman" id="sex-02">
        <label for="sex-02" class="input_radio">女性</label>
        <div class="row">
          <div class="col-sm-4">
            <p class="input_label">年齢：</p>
            <select class="input_list" name="age" size="1">
              <option value=""></option>
              <option value="10代">10代</option>
              <option value="20代">20代</option>
              <option value="30代">30代</option>
              <option value="40代">40代</option>
              <option value="50代">50代</option>
              <option value="60代">60代</option>
              <option value="それ以上">それ以上</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <p class="input_label">お住まい：</p>
            <select class="input_list" name="area" size="1">
              <option value=""></option>
              <option value="北海道">北海道</option>
              <option value="東北">東北</option>
              <option value="関東">関東</option>
              <option value="中部">中部</option>
              <option value="近畿">近畿</option>
              <option value="中国">中国</option>
              <option value="四国">四国</option>
              <option value="九州">九州</option>
            </select>
          </div>
        </div>
        <p class="input_label">サイトを知ったきっかけ：</p>
        <p>
          <input class="input_checkbox" type="checkbox" name="cue1" value="Facebook" id="cue-01">
          <label for="cue-01" class="check_css">Facebook</label>
          <input class="input_checkbox" type="checkbox" name="cue2" value="Twitter" id="cue-02">
          <label for="cue-02" class="check_css">Twitter</label>
          <input class="input_checkbox" type="checkbox" name="cue3" value="検索" id="cue-03">
          <label for="cue-03" class="check_css">検索したら出てきた</label>
          <input class="input_checkbox" type="checkbox" name="cue4" value="hadaのファン" id="cue-04">
          <label for="cue-04" class="check_css">hadaのファン</label>
        </p>
        <p class="input_label">お気に入りの記事：</p>
        <textarea class="input_textarea" type="text" name="favorite" rows="3" cols="80"></textarea>
        <p class="input_label">感想：</p>
        <textarea class="input_textarea" type="text" name="impressions" rows="5" cols="80"></textarea>
        <p class="input_label">その他hadaに伝えたいこと：</p>
        <textarea class="input_textarea" type="text" name="remarks" rows="4" cols="80"></textarea><br>

        <input type="hidden" name="check" value="check">
        <input class="button" type="submit" value="確認画面へ">
      </form>
    </div>
    <!-- //editモード -->

    <?PHP }elseif($mode=="check"){ ?>
    <!-- checkモード -->
    <div class="form check">
      <div class="form_area">
        <h3 class="input_label">ペンネーム：</h3>
        <p class="input_label"><?PHP echo($name); ?></p>
        <h3 class="input_label">性別：</h3>
        <p class="input_label"><?PHP echo($sex); ?></p>
        <h3 class="input_label">年齢：</h3>
        <p class="input_label"><?PHP echo($age); ?></p>
        <h3 class="input_label">お住まい：</h3>
        <p class="input_label"><?PHP echo($area); ?></p>
        <h3 class="input_label">サイトを知ったきっかけ：</h3>
        <p class="input_label"><?PHP if(isset($cue)){echo($cue);} ?></p>
        <h3 class="input_label">お気に入りのMEMO：</h3>
        <p class="input_label"><?PHP echo($favorite); ?></p>
        <h3 class="input_label">感想：</h3>
        <p class="input_label"><?PHP echo($impressions); ?></p>
        <h3 class="input_label">その他hadaに伝えたいこと：</h3>
        <p class="input_label"><?PHP echo($remarks); ?></p>

        <div class="row">
          <!-- DBに書き込む際の値をPOST -->
          <div class="col-sm-6">
            <form class="form_area" method="post" action="form.php">
        			<input type=hidden name="name" value="<?PHP echo($name); ?>">
              <input type=hidden name="sex" value="<?PHP echo($sex); ?>">
              <input type=hidden name="age" value="<?PHP echo($age); ?>">
              <input type=hidden name="area" value="<?PHP echo($area); ?>">
              <input type=hidden name="cue" value="<?PHP echo($cue); ?>">
              <input type=hidden name="favorite" value="<?PHP echo($favorite); ?>">
              <input type=hidden name="impressions" value="<?PHP echo($impressions); ?>">
              <input type=hidden name="remarks" value="<?PHP echo($remarks); ?>">

        			<input type="hidden" name="entry" value="entry">
        			<input class="button" type="submit" value="hadaに送信">
        		</form>
          </div>
          <!-- inputモードに遷移する際は入力された値をPOSTする -->
          <div class="col-sm-6">
            <form class="form_area" method="post" action="form.php">
        			<input type=hidden name="name" value="<?PHP echo($name); ?>">
              <input type=hidden name="sex" value="<?PHP echo($sex); ?>">
              <input type=hidden name="age" value="<?PHP echo($age); ?>">
              <input type=hidden name="area" value="<?PHP echo($area); ?>">
              <input type=hidden name="cue" value="<?PHP echo($cue); ?>">
              <input type=hidden name="favorite" value="<?PHP echo($favorite); ?>">
              <input type=hidden name="impressions" value="<?PHP echo($impressions); ?>">
              <input type=hidden name="remarks" value="<?PHP echo($remarks); ?>">
        			<input type="hidden" name="input" value="input">
        			<input class="button" type="submit" value="入力画面へ戻る">
        		</form>
          </div>
        </div>
      </div>
    </div>
    <!-- //checkモード -->

    <?PHP }elseif($mode=="input"){ ?>
		<!-- inputモード -->
    <div class="form">
      <form class="form_area" method="post" action="form.php">
        <p class="input_label">ペンネーム：</p>
        <input class="input_text" type="text" name="name" value="<?PHP echo($name); ?>">
        <p class="input_label">性別：</p>
        <input type="radio" name="sex" value="man" id="sex-01" <?php if($sex=='man'){ echo 'checked="checked"';} ?>>
        <label for="sex-01" class="input_radio">男性</label>
        <input type="radio" name="sex" value="woman" id="sex-02" <?php if($sex=='woman'){ echo 'checked="checked"';} ?>>
        <label for="sex-02" class="input_radio">女性</label>
        <div class="row">
          <div class="col-sm-4">
            <p class="input_label">年齢：</p>
            <select class="input_list" name="age" size="1">
              <option value=""></option>
              <option value="10代" <?php if($age=='10代'){ echo 'selected="selected"';} ?>>10代</option>
              <option value="20代" <?php if($age=='20代'){ echo 'selected="selected"';} ?>>20代</option>
              <option value="30代" <?php if($age=='30代'){ echo 'selected="selected"';} ?>>30代</option>
              <option value="40代" <?php if($age=='40代'){ echo 'selected="selected"';} ?>>40代</option>
              <option value="50代" <?php if($age=='50代'){ echo 'selected="selected"';} ?>>50代</option>
              <option value="60代" <?php if($age=='60代'){ echo 'selected="selected"';} ?>>60代</option>
              <option value="それ以上" <?php if($age=='それ以上'){ echo 'selected="selected"';} ?>>それ以上</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <p class="input_label">お住まい：</p>
            <select class="input_list" name="area" size="1">
              <option value=""></option>
              <option value="北海道" <?php if($area=='北海道'){ echo 'selected="selected"';} ?>>北海道</option>
              <option value="東北" <?php if($area=='東北'){ echo 'selected="selected"';} ?>>東北</option>
              <option value="関東" <?php if($area=='関東'){ echo 'selected="selected"';} ?>>関東</option>
              <option value="中部" <?php if($area=='中部'){ echo 'selected="selected"';} ?>>中部</option>
              <option value="近畿" <?php if($area=='近畿'){ echo 'selected="selected"';} ?>>近畿</option>
              <option value="中国" <?php if($area=='中国'){ echo 'selected="selected"';} ?>>中国</option>
              <option value="四国" <?php if($area=='四国'){ echo 'selected="selected"';} ?>>四国</option>
              <option value="九州" <?php if($area=='九州'){ echo 'selected="selected"';} ?>>九州</option>
            </select>
          </div>
        </div>
        <p class="input_label">サイトを知ったきっかけ：</p>
        <p>
          <input class="input_checkbox" type="checkbox" name="cue1" value="Facebook" id="cue-01" <?php if(isset($cue1Check)){ echo 'checked="checked"';} ?>>
          <label for="cue-01" class="check_css">Facebook</label>
          <input class="input_checkbox" type="checkbox" name="cue2" value="Twitter" id="cue-02" <?php if(isset($cue2Check)){ echo 'checked="checked"';} ?>>
          <label for="cue-02" class="check_css">Twitter</label>
          <input class="input_checkbox" type="checkbox" name="cue3" value="検索" id="cue-03" <?php if(isset($cue3Check)){ echo 'checked="checked"';} ?>>
          <label for="cue-03" class="check_css">検索したら出てきた</label>
          <input class="input_checkbox" type="checkbox" name="cue4" value="hadaのファン" id="cue-04" <?php if(isset($cue4Check)){ echo 'checked="checked"';} ?>>
          <label for="cue-04" class="check_css">hadaのファン</label>
        </p>
        <p class="input_label">お気に入りの記事：</p>
        <textarea class="input_textarea" type="text" name="favorite" rows="3" cols="80"><?PHP echo($favorite); ?></textarea>
        <p class="input_label">感想：</p>
        <textarea class="input_textarea" type="text" name="impressions" rows="5" cols="80"><?PHP echo($impressions); ?></textarea>
        <p class="input_label">その他hadaに伝えたいこと：</p>
        <textarea class="input_textarea" type="text" name="remarks" rows="4" cols="80"><?PHP echo($remarks); ?></textarea><br>

        <input type="hidden" name="check" value="check">
        <input class="button" type="submit" value="確認画面へ">
      </form>
    </div>
    <!-- //inputモード -->

    <?PHP }elseif($mode=="entry"){ ?>
		<!-- entryモード -->
    <div class="form">
      <h3 class="input_label">ご記入いただき、ありがとうございました</h3>
      <h3 class="input_label">引き続き、hadaとNUS3MEMOをどーぞよしなに</h3>
        <div class="col-sm-2">
        </div>
        <div class="col-sm-8">
          <a href="http://nus3.moo.jp/"><button class="button" type="button" name="button">TOPへ</button></a>
        </div>
        <div class="col-sm-2">
        </div>
      </div>
    </div>
    <!-- //entryモード -->
    <?PHP }?>

  </div>
  <!-- //contents -->

  <!-- footer -->
  <footer class="footer">
    <p class="copyWriter">&copy;NUS3 MEMO</p>
  </footer>
  <!-- //footer -->

</body>
</html>

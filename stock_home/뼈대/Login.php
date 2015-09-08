<?php

?>

<div class="loginForm">
  <form name="login" method="post" >
    <div class="inputForm">
      <label>
        <span>아이디</span>
        <input type="text" name="id" value="" onKeyPress="javascript:loginEnterSubmit();"/> <br />
      </label>
      <label>
        <span>비밀번호</span>
        <input type="password" name="password" value="" onKeyPress="javascript:loginEnterSubmit();"/>
      </label>
    </div>
    <a href="javascript:document.login.submit();" >
      <div class="buttonBlack" >
        <span class="white" >로그인</span>
      </div>
    </a>
  </form>
</div>

<script type="text/javascript" >
// <![CDATA[
function loginEnterSubmit() {
  if (event.keyCode == 13) { login.submit(); }
}
// ]]>
</script>
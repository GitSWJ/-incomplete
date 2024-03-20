<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/head.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}
include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

// 장바구니 상품수를 위한 sql
$sql = " select count(*) as cnt from cart where mem_id = '{$member['mb_id']}' AND state = 'Y' ";
$row = sql_fetch($sql);
$cart_count = $row['cnt'];

?>

<!-- 상단 시작 { -->
<div id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>
    <div id="skip_to_container"><a href="#container">본문 바로가기</a></div>

    <?php
    if(defined('_INDEX_')) { // index에서만 실행
        include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
    }
    ?>
    <div id="tnb">
    	
    </div>
    <!-- <div id="hd_wrapper"> -->
    <header class="top">
        <div id="logo">
            <a href="<?php echo G5_URL ?>"><p class="logo">T-PASS</p></a>
        </div>
        <div class="inner">
            <?php if(G5_COMMUNITY_USE) { ?>
                <ul id="hd_define">
                    <li class="top_link"><a href="<?php echo G5_URL ?>/product_order">상용제품</a></li>
                    <li class="top_link"><a href="<?php echo G5_URL ?>/">개발제품</a></li>
                    <li class="top_link"><a href="<?php echo G5_URL ?>/">기업연계</a></li>
                    <li class="top_link"><a href="<?php echo G5_URL ?>/">고객센터</a></li>
                    <li class="top_link"><a href="<?php echo G5_URL ?>/">T-PASS 소개</a></li>
                </ul>
            <?php } ?>
			<ul id="hd_qnb">
                
                <li>
                    <span class="cart_count"><?php echo $cart_count ?></span><a href="<?php echo G5_URL ?>/order_cart.php">장바구니</a>
                </li>
                <?php if ($is_member) {  ?>
                    <li><a href="<?php echo G5_URL ?>/order_list.php">주문리스트</a></li>
                    <li><a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php">정보수정</a></li>
                    <li><a href="<?php echo G5_BBS_URL ?>/logout.php">로그아웃</a></li>
                <?php if ($is_admin) {  ?>
                    <li class="tnb_admin"><a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>">관리자</a></li>
                <?php }  ?>
                <?php } else {  ?>
                    <li><a href="<?php echo G5_BBS_URL ?>/register.php">회원가입</a></li>
                    <li><a href="<?php echo G5_BBS_URL ?>/login.php">로그인</a></li>
                <?php }  ?>
	        </ul>
		</div>
                </header>
    
    <!-- <nav id="gnb">
        <h2>메인메뉴</h2>
        <div class="gnb_wrap">
            <ul id="gnb_1dul">
                <li class="gnb_1dli gnb_mnal"><button type="button" class="gnb_menu_btn" title="전체메뉴"><i class="fa fa-bars" aria-hidden="true"></i><span class="sound_only">전체메뉴열기</span></button></li>
                <?php
				$menu_datas = get_menu_db(0, true);
				$gnb_zindex = 999; // gnb_1dli z-index 값 설정용
                $i = 0;
                foreach( $menu_datas as $row ){
                    if( empty($row) ) continue;
                    $add_class = (isset($row['sub']) && $row['sub']) ? 'gnb_al_li_plus' : '';
                ?>
                <li class="gnb_1dli <?php echo $add_class; ?>" style="z-index:<?php echo $gnb_zindex--; ?>">
                    <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_1da"><?php echo $row['me_name'] ?></a>
                    <?php
                    $k = 0;
                    foreach( (array) $row['sub'] as $row2 ){

                        if( empty($row2) ) continue; 

                        if($k == 0)
                            echo '<span class="bg">하위분류</span><div class="gnb_2dul"><ul class="gnb_2dul_box">'.PHP_EOL;
                    ?>
                        <li class="gnb_2dli"><a href="<?php echo $row2['me_link']; ?>" target="_<?php echo $row2['me_target']; ?>" class="gnb_2da"><?php echo $row2['me_name'] ?></a></li>
                    <?php
                    $k++;
                    }   //end foreach $row2

                    if($k > 0)
                        echo '</ul></div>'.PHP_EOL;
                    ?>
                </li>
                <?php
                $i++;
                }   //end foreach $row

                if ($i == 0) {  ?>
                    <li class="gnb_empty">메뉴 준비 중입니다.<?php if ($is_admin) { ?> <a href="<?php echo G5_ADMIN_URL; ?>/menu_list.php">관리자모드 &gt; 환경설정 &gt; 메뉴설정</a>에서 설정하실 수 있습니다.<?php } ?></li>
                <?php } ?>
            </ul>
            <div id="gnb_all">
                <h2>전체메뉴</h2>
                <ul class="gnb_al_ul">
                    <?php
                    
                    $i = 0;
                    foreach( $menu_datas as $row ){
                    ?>
                    <li class="gnb_al_li">
                        <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_al_a"><?php echo $row['me_name'] ?></a>
                        <?php
                        $k = 0;
                        foreach( (array) $row['sub'] as $row2 ){
                            if($k == 0)
                                echo '<ul>'.PHP_EOL;
                        ?>
                            <li><a href="<?php echo $row2['me_link']; ?>" target="_<?php echo $row2['me_target']; ?>"><?php echo $row2['me_name'] ?></a></li>
                        <?php
                        $k++;
                        }   //end foreach $row2

                        if($k > 0)
                            echo '</ul>'.PHP_EOL;
                        ?>
                    </li>
                    <?php
                    $i++;
                    }   //end foreach $row

                    if ($i == 0) {  ?>
                        <li class="gnb_empty">메뉴 준비 중입니다.<?php if ($is_admin) { ?> <br><a href="<?php echo G5_ADMIN_URL; ?>/menu_list.php">관리자모드 &gt; 환경설정 &gt; 메뉴설정</a>에서 설정하실 수 있습니다.<?php } ?></li>
                    <?php } ?>
                </ul>
                <button type="button" class="gnb_close_btn"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div id="gnb_all_bg"></div>
        </div>
    </nav> -->
    <style>
        .top {
            /* Layout Properties */
            top: 0px;
            left: 0px;
            width: 1920px;
            height: 110px;
            /* UI Properties */
            background: #FFFFFF 0% 0% no-repeat padding-box;
            opacity: 1;
        }

        .logo {
            top: 0px;
            left: 26px;
            width: 135px;
            height: 45px;
            /* UI Properties */
            font: var(--unnamed-font-style-normal) normal var(--unnamed-font-weight-bold) 38px/45px SB Aggro;
            text-align: left;
            font: normal normal bold 38px/45px SB Aggro;
            letter-spacing: -3.04px;
            color: #0742B7;
        }

        .cart_count {
            top: 0px;
            left: -20px;
            width: 20px;
            height: 20px;
            font: normal normal normal 14px/20px NanumSquare;
            letter-spacing: 0px;
            color: #FFFFFF;
            background: #FF0000 0% 0% no-repeat padding-box;
            border-radius: 50%;
            opacity: 1;
            position: absolute;
            top: 0px;
            right: 0px;
            width: 20px;
            height: 20px;
            font: normal normal normal 14px/20px NanumSquare;
            letter-spacing: 0px;
            color: #FFFFFF;
            background: #FF0000 0% 0% no-repeat padding-box;
            border-radius: 50%;
            opacity: 1;
        }
        
    </style>
</div>
<!-- } 상단 끝 -->


<hr>

<!-- 콘텐츠 시작 { -->
<div id="wrapper">
    <div id="container_wr">
   
    <div id="container">
        <?php if (!defined("_INDEX_")) { ?><h2 id="container_title"><span title="<?php echo get_text($g5['title']); ?>"><?php echo get_head_title($g5['title']); ?></span></h2><?php }
<?php 
    /**
     * @file  index.php
     * @author zero <zero@zeroboard.com>
     * @brief 시작 페이지
     *
     * Request Argument에서 mid, act로 module 객체를 찾아서 생성하고 \n
     * 모듈 정보를 세팅함
     *
     * @mainpage XpressEngine 
     * @section intro 소개
     * XE 는 오픈 프로젝트로 개발되는 오픈 소스입니다.\n
     * 자세한 내용은 아래 링크를 참조하세요.
     * - 공식홈페이지        : http://www.xpressengine.com
     * - SVN Repository      : http://svn.xpressengine.com/XpressEngine/trunk
     * \n
     * "XpressEngine (XE)"은 자유 소프트웨어입니다. \n
     * 소프트웨어의 피양도자는 자유 소프트웨어 재단이 공표한 GNU 일반 공중 사용 허가서 2판 또는 \n
     * 그 이후 판을 임의로 선택해서, 그 규정에 따라 프로그램을 개작하거나 재배포할 수 있습니다. \n
     * \n
     * 이 프로그램은 유용하게 사용될 수 있으리라는 희망에서 배포되고 있지만, 특정한 목적에 맞는 적합성 \n
     * 여부나 판매용으로 사용할 수 있으리라는 묵시적인 보증을 포함한 어떠한 형태의 보증도 제공하지 않습니다. \n
     * 보다 자세한 사항에 대해서는 GNU 일반 공중 사용 허가서를 참고하시기 바랍니다. \n
     * \n
     * GNU 일반 공중 사용 허가서는 이 프로그램과 함께 제공됩니다. 만약, 이 문서가 누락되어 있다면 자유 소프트웨어\n
     * 재단으로 문의하시기 바랍니다. \n
     * (자유 소프트웨어 재단: Free Software Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA) 
     *
     **/

    /**
     * @brief 기본적인 상수 선언,  웹에서 직접 호출되는 것을 막기 위해 체크하는 상수 선언
     **/
    define('__ZBXE__', true);

    /**
     * @brief 필요한 설정 파일들을 include
     **/
    require_once('./config/config.inc.php');

    /**
     * @brief Context 객체를 생성하여 초기화
     * 모든 Request Argument/ 환경변수등을 세팅
     **/
    $oContext = &Context::getInstance();
    $oContext->init();

    /**
     * @brief default_url 이 설정되어 있고 현재 url이 default_url과 다르면 SSO인증을 위한 rediret 시도 후 모듈 동작
     **/
    if($oContext->checkSSO()) {
        $oModuleHandler = new ModuleHandler();
        if($oModuleHandler->init()) {
            $oModule = &$oModuleHandler->procModule();
            $oModuleHandler->displayContent($oModule);
        }
    }
    $oContext->close();
?>

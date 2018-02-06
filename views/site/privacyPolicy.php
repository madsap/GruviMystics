<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use \yii\helpers\Url;
use app\assets\AppAsset;
use \app\models\User;
use app\components\widgets\CallDetails;
use app\components\widgets\Twilio;
use app\components\widgets\AddGruviBucks;
use app\components\widgets\ExpiredSessionAlert;
use app\components\widgets\BlockUserAlert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!--
        <link href="css/default.css" rel="stylesheet" type="text/css" />-->
        <script src="<?= Url::to(["/css/bootstrap/js/bootstrap.min.js"], true); ?>" type="text/javascript"></script>
        <link href="<?= Url::to(["/css/bootstrap/css/bootstrap.min.css"], true); ?>" rel="stylesheet" type="text/css" />

<?= Html::csrfMetaTags() ?>
        <title>Privacy Policy</title>
        <?php $this->head() ?>
        <style>
            .text-indent{
                text-indent: 30px;
            }
            .text-indent-2{
                text-indent: 60px;
            }
        </style>
    </head>
    <body>
<?php $this->beginBody() ?>

        <div class="wrap">
            <header>
                <div class="container" style="position:relative;">
                    <nav class="navbar navbar-default">
                        <div class="navbar-header">
                            <a class="navbar-brand" href="javascript:void(0);"><img src="<?= Url::to(['/images/logo.png'], true); ?>" alt="" class="img-responsive"/></a>
                        </div>
                    </nav>
                </div>
            </header>


                <div class="page-wrapper">
                    <div class="page-title text-default h3">Privacy Policy</div>
                    <div class="page-container panel panel-default">
                        <div class="panel-body text-violet">
                            <h3 class="text-bold text-pink">Last Revised: March 22, 2016</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent">
                                        Gruvi Mystics. (​ <b>"we"</b>​ or ​ <b>"GruviMystics.com"</b>​ ) has created this privacy policy (​ <b>"Privacy Policy"</b>​ ) to 
                                        demonstrate our commitment to privacy. This Privacy Policy applies to ​ <a href="http://www.gruvimystics.com">www.gruvimystics.com</a>​ , owned and 
                                        operated by Gruvi Mystics, and to all Mobile and Facebook Apps operated by Gruvi Mystics. This privacy 
                                        policy describes how <a href="http://www.gruvimystics.com">GruviMystics.com</a> collects and uses the Personal Data you provide on our website: 
                                        <a href="http://www.gruvimystics.com">www.GruviMystics.com</a>​ , and in our Mobile and Facebook Apps, as well as the choices available to you 
                                        regarding our use of your personal data and how you can access and update this information. We realize 
                                        the importance of feeling safe online and want you to feel comfortable using our personalized products 
                                        and services (collectively, ​ <b>"Offerings"</b>​ ) and exchanging information on websites of <a href="http://www.gruvimystics.com">GruviMystics.com</a>, its 
                                        affiliates or agents (​ <b>"Company"</b>​ or ​ <b>"We"</b>​ ) with links to this Privacy Policy (collectively, the ​ <b>"Website"</b>​ ) and 
                                        our mobile applications (the ​ <b>"Application(s)"</b>​ ). 
                                    </p>
                                </div>
                            </div>
                            <h3 class="text-bold text-pink">1. QUESTIONS; CONTACTING COMPANY; REPORTING VIOLATIONS </h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent">
                                        If you have any questions, concerns or complaints about our Privacy Policy or our data collection or 
                                        processing practices, or if you want to report any security violations to us, please contact us at the 
                                        following address or phone number: 
                                    </p>
                                    <br>
                                    <p class="text-indent"><a href="http://www.gruvimystics.com">GruviMystics.com </a></p>
                                    <br>
                                    <p class="text-indent">5059 Moor Park Ave </p>
                                    <br>
                                    <p class="text-indent">San Jose, CA 95129</p>
                                    <br>
                                    <p class="text-indent">Email:<a href="mailto:info@gruvimystics.com">info@gruvimystics.com</a></p>
                                    <br>
                                    <p class="text-indent">Tel:925.998.7187</p>
                                    <br>
                                </div>
                            </div>

                            <h3 class="text-bold text-pink">2. USER CONSENT</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent">By submitting Personal Data through our Website, Application or Offerings, you agree to the terms of this 
                                        Privacy Policy and you expressly consent to the collection, use and disclosure of your Personal Data in 
                                        accordance with this Privacy Policy. 
                                    </p>
                                </div>
                            </div>

                            <h3 class="text-bold text-pink">3. A NOTE TO USERS OUTSIDE OF THE UNITED STATES </h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent">If you are a non-U.S. user of the Website, Application(s) or Offerings (the "Company Properties"), by 
                                        visiting the Website or providing us with data, you acknowledge and agree that your Personal Data 
                                        may be processed for the purposes identified in the Privacy Policy. In addition, your Personal Data 
                                        may be processed in the country in which it was collected and in other countries, including the United States, where laws regarding processing of Personal Data may be less stringent than the laws in your 
                                        country. By providing your data, you consent to such transfer.
                                    </p>
                                </div>
                            </div>

                            <h3 class="text-bold text-pink">4. TYPES OF DATA WE COLLECT</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent">We collect Personal Data and Anonymous Data from you as described below when you visit our Website, 
                                        when you send us information or communications, and/or when you use our Application(s) and Offerings. 
                                        <b>"Personal Data"</b>​ means data that allows someone to identify or contact you, including, for example, your 
                                        name, address, telephone number, e-mail address, as well as any other non-public information about you 
                                        that is associated with or linked to any of the foregoing data. ​ <b>"Anonymous Data"</b>​ means data that is not 
                                        associated with or linked to your Personal Data; Anonymous Data does not permit the identification of 
                                        individual persons. We may also collect or develop aggregated information, which may include Anonymous 
                                        Data and Personal Data, provided that such information does not personally identify you or any of our 
                                        other users (​ <b>"Aggregated Data"</b>​ ). 
                                    </p>
                                    <p class="text-indent"><b>  A. Personal Data That You Provide to Us.</b>​ In order to provide you with personalized services and 
                                        features we ask you to register and establish your personal profile (​ <b>"Profile"</b>​ ). During the registration 
                                        process we will ask you for Personal Data such as your name, telephone number, gender, birth information, 
                                        e-mail address, password, place of residence, zip code, etc. This Personal Data is used to customize the 
                                        Offerings to you and to provide you with personalized services. Depending on the Offerings you are 
                                        accessing, you may be asked at various times, for example, by filling out a form or survey, to provide 
                                        Personal Data such as name, gender, e-mail address, birth date, and birth location. Personal Data is 
                                        collected primarily to make it easier and more rewarding for you to use our Offerings. 
                                    </p>
                                    <p class="text-indent"><b>  B. Personal Data Collected via Technology.</b>​ To make our Company Properties more useful to you,
                                        our servers (which may be hosted by a third party service provider) collect Personal Data from you, 
                                        including browser type, Internet Protocol (IP) address (a number that is automatically assigned to your 
                                        computer when you use the Internet, which may vary from session to session), domain name, and/or a 
                                        date/time stamp for your visit. Like most Internet services, we automatically gather this Personal Data and 
                                        store it in log files each time you visit our Website. 
                                    </p>
                                    <p class="text-indent"><b>  C. Application(s).</b>​ If you make purchase through the Company Properties we may collect and 
                                        store certain information about you to process your purchases and populate forms for future transactions, 
                                        such as your telephone number, address, e-mail address, and credit card information. This information may 
                                        be shared with third parties who help process and fulfill your purchases. When you submit credit card 
                                        numbers, we encrypt that information using industry standard technology.
                                    </p>
                                    <p class="text-indent"><b>  E. Personal Data That We Collect from You About Others.</b>​ If you decide to submit information 
                                        about a third party, we will collect the third party's Personal Data in order to personalize the services for 
                                        such third party such as our "Tell a Friend" feature. If you choose to use our referral service to tell a friend 
                                        about our website, we will ask you for your friend's name and email address. We will automatically send 
                                        your friend a one-time email inviting him or her to visit the website. <a href="http://www.gruvimystics.com">www.GruviMystics.com</a> stores this 
                                        information for the sole purpose of sending this one-time email and tracking the success of our referral 
                                        program. You or the third party may contact us to request the removal of this information from our 
                                        database by contacting us at ​ <a href="mailto:info@gruvimystics.com">info@gruvimystics.com</a> 
                                    </p>
                                    <p class="text-indent"><b>  F. Advertising.</b>​ If there is advertising on the Company Properties, such advertising may employ the 
                                        use of Cookies or other methods to track hits and clickthroughs. We are not responsible for advertiser 
                                        Cookies or how the information gathered through their use might be used.
                                    </p>
                                    <p class="text-indent"><b>  G. Children.</b>​ We do not intentionally gather Personal Data from users who are under the age of 13. If a 
                                        child under 13 submits Personal Data to Company and we learn that the Personal Data is the information of 
                                        a child under 13, we will attempt to delete the information as soon as possible. If you believe that we might 
                                        have any Personal Data from a child under 13, please contact us at ​ <a href="mailto:info@gruvimystics.com">info@gruvimystics.com</a> 
                                    </p>
                                    <p class="text-indent"><b>  H. Information You Provide to Facebook and Other Social Networking Sites.​</b>​ 
                                        The Company Properties may allow users to access Facebook to interact with friends and to share on Facebook through 
                                        Wall and friends' News Feeds. If you are already logged into the Company Properties and Facebook or 
                                        another SNS, when you click on "Connect with Facebook," or a similar connection on another SNS, you will 
                                        be prompted to merge your profiles. If you are already logged into the Company Properties but not logged 
                                        into Facebook or another SNS that we support, when you click on "Connect with Facebook," or a similar 
                                        connection on another SNS, you will be prompted to enter your SNS credentials or to "Sign Up" for the SNS. 
                                        By proceeding, you are allowing the Company Properties to access your information and you are agreeing 
                                        to the Facebook or other SNS's Terms of Use in your use of the Company Properties. Conversely, if you are 
                                        not currently registered as a user of the Company Properties, and you click on "Sign in" using Facebook or 
                                        another SNS that we support, you will first be asked to enter your Facebook or SNS credentials and then be 
                                        given the option to register for the Company Properties. In this case, we may receive information from 
                                        Facebook or another SNS to make it easier for you to create an account (​ <b>"Account"</b>​ ) on the Company 
                                        Properties and show our relevant content from your Facebook or SNS friends. Once you register on the 
                                        Company Properties and connect with Facebook or another SNS you will be able to automatically post 
                                        recent activity back to Facebook or the other SNS. Any information that we collect from your Facebook or 
                                        other SNS account may depend on the privacy settings you have with that SNS, so please consult the SNS's 
                                        privacy and data practices. You have the option to disable Facebook Connect at any time by logging into 
                                        your Account through the Company Properties and going to settings, "About Me," "Linked Accounts," and 
                                        then unselecting "Facebook." Further, you can edit privacy settings for the reviews that appear on 
                                        Facebook, or disconnect your Company Properties activity stream by visiting the Facebook Applications 
                                        Settings page. 
                                    </p>
                                    <p class="text-indent"><b>  I. Information Collected Via Technology.​</b>​ </p>
                                    <p class="text-indent-2">
                                        • <b>Information Collected by Our Servers.</b>​ To make our Company Properties more useful to you, our 
                                        servers (which may be hosted by a third party service provider) collect information from you, including your 
                                        browser type, operating system, Internet Protocol (​ <b>"IP"</b>​ ) address (a number that is automatically assigned 
                                        to your computer when you use the Internet, which may vary from session to session), domain name, and/or 
                                        a date/time stamp for your visit. 
                                    </p>
                                    <p class="text-indent-2">
                                        • <b>Log Files.</b>​ As is true of most websites , we gather certain information automatically and store it in 
                                        log files. This information includes IP addresses, browser type, Internet service provider (​ <b>"ISP"</b>​ ), 
                                        referring/exit pages, operating system, date/time stamp, and clickstream data. We use this information to 
                                        analyze trends, administer the Website, track users' movements around the Website, gather demographic 
                                        information about our user base as a whole, and better tailor our Offerings to our users' needs. For 
                                        example, some of the information may be collected so that when you visit the Website or the Offerings 
                                        again, it will recognize you and the information could then be used to serve advertisements and other 
                                        information appropriate to your interests. Except as noted in this Privacy Policy, we do not link this 
                                        automatically-collected data to Personal Data. 
                                    </p>
                                    <p class="text-indent-2">
                                        • <b>Cookies.</b>​ Like many online services, we use Cookies to collect information. <b>"Cookies"</b>​ are small 
                                        pieces of information that a website sends to your computer's hard drive while you are viewing the 
                                        website. We may use both session Cookies (which expire once you close your web browser) and persistent 
                                        Cookies (which stay on your computer until you delete them) to provide you with a more personal and interactive experience on our Website. This type of information is collected to make the Website more 
                                        useful to you and to tailor the experience with us to meet your special interests and needs.
                                    </p>
                                    <p class="text-indent-2">
                                        • ​ <b>Pixel Tags.</b>​ In addition, we use ​ <b>"Pixel Tags"</b>​ (also referred to as clear Gifs, Web beacons, or Web 
                                        bugs). Pixel Tags are tiny graphic images with a unique identifier, similar in function to Cookies that are 
                                        used to track online movements of Web users. In contrast to Cookies, which are stored on a user's 
                                        computer hard drive, Pixel Tags are embedded invisibly in Web pages. Pixel Tags also allow us to send 
                                        e-mail messages in a format users can read, and they tell us whether e-mails have been opened to ensure 
                                        that we are sending only messages that are of interest to our users. We may use this information to reduce 
                                        or eliminate messages sent to a user. We do not tie the information gathered by Pixel Tags to our users' 
                                        Personal Data. 
                                    </p>
                                    <p class="text-indent-2">
                                        • ​ <b>Collection of Data by Advertisers.</b>​ We may also use third parties to serve ads on the Company 
                                        Properties. Certain third parties may automatically collect information about your visits to the Company 
                                        Properties and other websites, your IP address, your ISP, the browser you use to visit our Company 
                                        Properties (but not your name, address, e-mail address or telephone number). They do this by using 
                                        Cookies, Pixel Tags or other technologies. Information collected may be used, among other things, to 
                                        deliver advertising targeted to your interests and to better understand the usage and visitation of our 
                                        Website and the other sites tracked by these third parties. This policy does not apply to, and we are not 
                                        responsible for, Cookies or Pixel Tags in third party ads, and we encourage you to check the privacy policies 
                                        of advertisers and/or ad services to learn about their use of Cookies and other technologies. You may opt 
                                        out of receiving interest-based advertising from some of our partners by 
                                        visiting​ <a href="http://www.aboutads.info/choices">http://www.aboutads.info/choices</a>​ or ​ <a href="http://www.networkadvertising.org/managing/opt_out.asp">http://www.networkadvertising.org/managing/opt_out.asp.</a> 
                                        We do not currently respond to "Do Not Track" signals from web browsers. 
                                    </p>
                                    <p class="text-indent-2">
                                        • ​ <b>Flash LSOs.</b>​ When we post videos, third parties may use local shared objects, known as ​ <b>"Flash 
                                            Cookies,"</b>​ to store your preferences for volume control or to personalize certain video features. Flash 
                                        Cookies are different from browser Cookies because of the amount and type of data and how the data is 
                                        stored. Cookie management tools provided by your browser will not remove Flash Cookies. To learn how to 
                                        manage privacy and storage settings for Flash Cookies, click here: 
                                        <a href="http://www.macromedia.com/support/documentation/en/flashplayer/help/settings_manager07.html">http://www.macromedia.com/support/documentation/en/flashplayer/help/settings_manager07.html</a>
                                    </p>
                                    <p class="text-indent-2">
                                        • ​ <b>Mobile Services.</b>​ We may also collect non-personal information from your mobile device if you 
                                        have downloaded our Application(s). This information is generally used to help us deliver the most relevant 
                                        information to you. Examples of information that may be collected and used include your geographic 
                                        location, how you use the Application(s), and information about the type of device you use. In addition, in 
                                        the event our Application(s) crashes on your mobile device, we will receive information about your mobile 
                                        device model software version and device carrier, which allows us to identify and fix bugs and otherwise 
                                        improve the performance of our Application(s). This information is sent to us as aggregated information 
                                        and is not traceable to any individual and cannot be used to identify an individual. 
                                    </p>
                                    <p class="text-indent-2">
                                        • ​ <b>Google Analytics.</b>​ We use Google Analytics to help analyze how users use the Website. Google 
                                        Analytics uses Cookies to collect information such as how often users visit the Website, what pages they 
                                        visit, and what other sites they used prior to coming to the Website. We use the information we get from 
                                        Google Analytics only to improve our Website and services. Google Analytics collects only the IP address 
                                        assigned to you on the date you visit the Website, rather than your name or other personally identifying 
                                        information. We do not combine the information generated through the use of Google Analytics with your 
                                        Personal Data. Although Google Analytics plants a persistent Cookie on your web browser to identify you 
                                        as a unique user the next time you visit the Website, the Cookie cannot be used by anyone but Google. 
                                        Google's ability to use and share information collected by Google Analytics about your visits to the Website 
                                        is restricted by the Google Analytics Terms of Use and the Google Privacy Policy. 
                                    </p>
                                </div>
                            </div>

                            <h3 class="text-bold text-pink">5. USE OF YOUR DATA</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent"><b>A. General.​ </b>In general, Personal Data you submit to us is used either to respond to requests that you
                                        make, or to aid us in serving you better. We use your Personal Data to personalize our Offerings; to deliver 
                                        personalized information to you; to send you administrative e-mail notifications, such as security or support 
                                        and maintenance advisories; to provide you with electronic newsletters; and to send you special offers 
                                        related to our Offerings and for other marketing purposes of Company or our third party partners, should 
                                        you opt-in to receive such communications by indicating such preference in your Profile. Your e-mail 
                                        address together with a password is used to secure your Profile and to make our personalized e-mail 
                                        services available to you.  
                                    </p>

                                    <p class="text-indent"><b>B. Creation and Use of Anonymous and Aggregated Data.​ </b>We may create Anonymous Data and 
                                        Aggregated Data from Personal Data by excluding information (such as your name) that makes the data 
                                        personally identifiable to you. We use this Anonymous Data and Aggregated Data for trend analysis, and to 
                                        better understand patterns of usage so that we may enhance the content of our Offerings and improve 
                                        Website navigation. We reserve the right to use and disclose Anonymous Data and Aggregated Data to 
                                        third party companies in our discretion. For example, demographic and profile data is shared with our 
                                        advertisers only in the form of Aggregated Data. Aggregated Data helps our advertisers to tailor their 
                                        services to the collective characteristics of users of the Company Properties.   
                                    </p>

                                    <p class="text-indent"><b>C. IP Addresses.​</b>We use your IP Address to help diagnose problems with our server, to administer the 
                                        Website, and to track trends and statistics.   
                                    </p>

                                    <p class="text-indent"><b>D. Feedback.​</b>If you provide feedback on any of our Company Properties to us, we may use such 
                                        feedback for any purpose, provided we will not associate such feedback with your Personal Data. We will 
                                        collect any information contained in such communication and will treat the Personal Data in such 
                                        communication in accordance with this Privacy Policy.   
                                    </p>
                                </div>
                            </div>

                            <h3 class="text-bold text-pink">6. DISCLOSURE OF YOUR PERSONAL DATA</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent">
                                        <b>A.</b>​ We will share your Personal Data with third parties only in the ways that are described in this Privacy
                                        Policy. We do not sell your Personal Data to third parties. 
                                    </p>
                                    <p class="text-indent">
                                        <b>B. Corporate Restructuring.</b>​ We may share some or all of your Personal Data in connection with or 
                                        during negotiation of any merger, financing, acquisition or dissolution, transition, or proceeding involving 
                                        sale, transfer, divestiture, or disclosure of all or a portion of our business or assets. In the event of an 
                                        insolvency, bankruptcy or receivership, Personal Data may also be transferred as a business asset. If 
                                        another company acquires our Company, business or assets, that company will possess the Personal Data 
                                        collected by us and will assume the rights and obligations regarding your Personal Data described in this 
                                        Privacy Policy. 
                                    </p>

                                    <p class="text-indent">
                                        <b>C. Co-Branded Service Offerings.</b>​ We may enter into agreements with certain businesses (each, a 
                                        <b>"Partner Company"</b>​ ) pursuant to which we provide the Partner Company with a URL and a customer 
                                        registration page co-branded with, or private labeled by, the Partner Company, and the Partner Company 
                                        distributes and promotes the URL to its customers. A Partner Company may want access to Personal Data 
                                        that we collect from its customers. As a result, if you register on a website through a Partner Company, we 
                                        may provide your Personal Data to the Partner Company. Because we do not control the privacy practices 
                                        of our Partner Companies, you should read and understand their privacy policies.
                                    </p>

                                    <p class="text-indent">
                                        <b>D. Third Party Services.</b>​ We reserve the right to offer services and products from third party vendors
                                        (​ <b>"Third Party Vendors"</b>​ ) to you on the Company Properties, such as via newsletters or special offers, based 
                                        on the preferences that you identify during the registration process and based on your subsequent 
                                        preferences. We may share Aggregated Data about our user base with these Third Party Vendors; this 
                                        information does not identify individual users. We do not link Aggregated Data with Personal Data. 
                                    </p>

                                    <p class="text-indent">
                                        <b>E. Third Party Contractors.</b>​ We may share your Personal Data with third party contractors or service 
                                        providers (​ <b>"Third Party Contractors"</b>​ ) to provide you with the services that we offer you though the 
                                        Company Properties; to conduct quality assurance testing; to provide technical support; or to provide 
                                        specific services in accordance with your instructions. These Third Party Contractors are required not to use 
                                        your Personal Data other than to provide the services requested by us. You expressly consent to the 
                                        sharing of your Personal Data with our Third Party Contractors for the sole purpose of providing the 
                                        Company Properties to you.  
                                    </p>

                                    <p class="text-indent">
                                        <b>F. Social Networking Sites.</b>​ Some of our Applications and Offerings may enable you to post content 
                                        to SNSs (e.g., Facebook, Instagram or Twitter). If you choose to do this, we will provide information to such 
                                        SNSs in accordance with your elections. You acknowledge and agree that you are solely responsible for 
                                        your use of those websites and that it is your responsibility to review the terms of use and privacy policy of 
                                        the third party provider of such SNSs. We will not be responsible or liable for: (i) the availability or accuracy 
                                        of such SNSs; (ii) the content, products or services on or availability of such SNSs; or (iii) your use of any such 
                                        SNSs.
                                    </p>

                                    <p class="text-indent">
                                        <b>G. Other Disclosures.</b>​ Due to the existing legal regulatory and security environment, we cannot fully 
                                        ensure that your private communications and other Personal Data will not be disclosed to third parties 
                                        under certain circumstances. For example, we may be forced to disclose information to the government or 
                                        third parties under court order, subpoena, or other circumstances, or third parties may unlawfully intercept 
                                        or access transmissions or private communications. Additionally, in the unlikely event we need to 
                                        investigate or resolve possible problems or inquiries, we can (and you authorize us to do so) disclose any 
                                        information about you to private entities, law enforcement or other government officials as we, in our sole 
                                        discretion, believe necessary or appropriate. In addition, where we ask for your Personal Data and you are 
                                        notified that the information we are collecting on that page will be shared with third parties, and in cases 
                                        where you opt-in to share your Personal Data with third parties, those disclosures and opt-ins will override 
                                        anything to the contrary in this Privacy Policy. Except as set forth above, we will not disclose any of your 
                                        Personal Data to third parties unless you give us your prior express consent. 
                                    </p>
                                </div>
                            </div>

                            <h3 class="text-bold text-pink">7. SECURITY</h3>
                            <div class="row">
                                <p class="text-indent">
                                    We have implemented and follow reasonable industry standard technical and procedural measures to 
                                    protect against unauthorized access and use of your personal information. However, you should know that 
                                    neither we nor any other website can fully eliminate these risks. 
                                </p>
                            </div>

                            <h3 class="text-bold text-pink">8. OTHER WEBSITE LINKS</h3>
                            <div class="row">
                                <p class="text-indent">
                                    The Company Properties contain links to other websites. Our provision of a link to any other website or 
                                    location, such as Partner Company websites, is for your convenience and does not signify our endorsement 
                                    of such other website or location or its contents. When you click on such a link, you will leave the Company 
                                    Properties and go to another website. During this process, another entity may collect Personal Data or Anonymous Data from you. Company has no control over, does not review, and is not responsible for the 
                                    privacy practices or the content of such other websites. Please be aware that the terms of this Privacy 
                                    Policy do not apply to these outside websites or content, or to any collection of data after you click on links 
                                    to such outside websites. 
                                </p>
                            </div>

                            <h3 class="text-bold text-pink">9. SOCIAL MEDIA FEATURES AND WIDGETS</h3>
                            <div class="row">
                                <p class="text-indent">
                                    Our Web site includes Social Media Features, such as the Facebook Like button and Widgets, such as the 
                                    Share this button or interactive mini-programs that run on our site. These Features may collect your IP 
                                    address, which page you are visiting on our site, and may set a cookie to enable the Feature to function 
                                    properly. Social Media Features and Widgets are either hosted by a third party or hosted directly on our 
                                    Site. Your interactions with these Features are governed by the privacy policy of the company providing it.  
                                </p>
                            </div>

                            <h3 class="text-bold text-pink">10. UPDATING/DELETING YOUR PROFILE</h3>
                            <div class="row">
                                <p class="text-indent">
                                    <b>A. Changes to Personal Data.​</b>You can view the Personal Data you previously provided by viewing 
                                    your Profile from your personalized Home Page. You can delete your Profile at any time by clicking on the 
                                    "delete profile" link. If your personal data changes, you may correct or update, by emailing us at 
                                    <a href="mailto:info@gruvimystics.com">info@gruvimystics.com</a>​ or by contacting us by telephone or postal mail at the contact information listed 
                                    below. We will respond to your request to access within 30 days. You may request deletion of your Personal 
                                    Data by us, but please note that we may be required (by law or otherwise) to keep this information and not 
                                    delete it (or to keep this information for a certain time, in which case we will comply with your deletion 
                                    request only after we have fulfilled such requirements). When we delete any information, it will be deleted 
                                    from the active database, but may remain in our archives.
                                </p>
                                <p class="text-indent">
                                    <b>B. Opt-out.​</b>If you have chosen to receive personalized messages, newsletters, or promotional 
                                    communications by e-mail, you can at any time cancel this, or "opt-out," by following the instructions on 
                                    your personalized Home Page or by following the unsubscribe instructions provided in the e-mail you 
                                    receive. Should you decide to opt-out of receiving future mailings, we may share your e-mail address with 
                                    our Third Party Contractors to ensure that you do not receive further communications from us. Despite 
                                    your indicated e-mail preferences, we may send you service-related communications, including notices of 
                                    any updates to our Terms of Service or Privacy Policy. 
                                </p>
                                <p class="text-indent">
                                    <b>C. Cookies.​</b>If you decide at any time that you no longer wish to accept Cookies from the Company 
                                    Properties for any of the purposes described above, then you can instruct your browser, by changing its 
                                    settings, to stop accepting Cookies or to prompt you before accepting a Cookie from the websites you visit. 
                                    Consult your browser's technical information. If you do not accept Cookies, however, you may not be able 
                                    to use all portions of the Company Properties or all functionality of the Company Properties. If you have 
                                    any questions about how to disable or modify Cookies, please let us know at the contact information 
                                    provided below. 
                                </p>
                                <p class="text-indent">
                                    <b>D. De-Linking SNSs.​</b>If you decide at any time that you no longer wish to have your SNS account (e.g., 
                                    Facebook or Twitter) linked to your Account, then you may de-link the SNS account in the "preferences" 
                                    section in your Account settings. You may also manage the sharing of certain Personal Data with us when 
                                    you connect with us through an SNS, such as through Facebook Connect. Please refer to the privacy 
                                    settings of the SNS to determine how you may adjust our permissions and manage the interactivity 
                                    between the Company Properties and your social media account or mobile device.
                                </p>
                                <p class="text-indent">
                                    <b>E. Applications.​</b>You can stop all collection of information by the Application(s) by uninstalling the 
                                    Application(s). You may use the standard uninstall processes as may be available as part of your mobile 
                                    device or via the mobile application marketplace or network.
                                </p>
                            </div>

                            <h3 class="text-bold text-pink">11. SERVICE-RELATED ANNOUNCEMENTS</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent">Despite your indicated e-mail preferences, we may send you service-related announcements (such as, but 
                                        not limited to, notice that we have changed the Offerings, the Terms of Service, or the Privacy Policy) when 
                                        we believe it is necessary to do so. You may not opt-out of these communications, which are not 
                                        promotional in nature, but if you do not wish to receive these announcements, you have the option to 
                                        delete your Profile. We will not have any liability whatsoever to you for any deletion of your Profile. 
                                    </p>
                                </div>
                            </div>

                            <h3 class="text-bold text-pink">12. DISPUTE RESOLUTION</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent">If you believe that we have not adhered to this Privacy Policy, please contact us by e-mail at 
                                        <a href="mailto:info@gruvimystics.com">info@gruvimystics.com</a>​ . We will do our best to address your concerns. If you feel that your complaint has 
                                        been addressed incompletely, we invite you to let us know for further investigation. If you and Company 
                                        are unable to reach a resolution to the dispute, you and Company will settle the dispute exclusively under 
                                        the rules of the American Arbitration Association (<a href="http://www.adr.org">www.adr.org</a>). 
                                    </p>
                                </div>
                            </div>

                            <h3 class="text-bold text-pink">13. CHANGES TO THIS PRIVACY POLICY</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent">We may amend this Privacy Policy at any time in our sole discretion. We will notify you of any changes to 
                                        our Privacy Policy by posting the new Privacy Policy on our Website, and we will change the "Last Updated" 
                                        date above. If we make any material changes we will notify you by email (sent to the e-mail address 
                                        specified in your account) or by means of a notice on this Site prior to the change becoming effective. You 
                                        should consult this Privacy Policy regularly for any changes. 
                                    </p>
                                </div>
                            </div>

                            <h3 class="text-bold text-pink">14. CONTACTING US</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-indent">
                                        If you have any questions about this Privacy Policy, the practices of the Company Properties, or your 
                                        dealings with the Company Properties, you can contact us using the following contact information: 
                                    </p>
                                    <br>
                                    <p class="text-indent"><a href="http://www.gruvimystics.com">www.GruviMystics.com</a> </p>
                                    <br>
                                    <p class="text-indent">5059 Moor Park Ave </p>
                                    <br>
                                    <p class="text-indent">San Jose, CA 95129</p>
                                    <br>
                                    <p class="text-indent">Email:<a href="mailto:info@gruvimystics.com">info@gruvimystics.com</a></p>
                                    <br>
                                    <p class="text-indent">Tel:925.998.7187</p>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">
                    <a class="navbar-brand" href="#"><img src="<?= Url::to(['/images/logo.png'], true); ?>" alt="" class="img-responsive"/></a>
                    &copy; <?= date('Y') ?>-<?= Yii::$app->name ?> 
                </p>

                <p class="pull-right">
<?= Html::a('Privacy Policy', Url::to(['site/privacy-policy'])) ?>
                    |
                    <?= Html::a('Terms & Service', Url::to(['site/terms-and-service'])) ?>
                </p>
            </div>
        </footer>
<?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
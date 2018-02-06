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
        <title>Terms & Conditions</title>
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
            <?php
            $this->title = 'Terms & Conditions';
            ?>
            <div class="page-wrapper">
                <div class="page-title text-default h3"><?= Html::encode($this->title) ?></div>
                <div class="page-container panel panel-default">
                    <div class="panel-body text-violet">
                        <h3 class="text-bold text-pink">TERMS & CONDITIONS</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent">
                                    <b>Gruvi Mystics (hereinafter referred to as 'Company' or 'Gruvi Mystics') is committed to protecting its members' 
                                        privacy. The Company takes all appropriate security measures to protect all personal information against 
                                        unauthorized access, unauthorized alteration, disclosure or destruction. </b>
                                </p>
                                <p>
                                    <b>By entering and/or registering on the website You give Your consent to and accept the following:</b>
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">1ST CLAUSE: GruviMystics.com - CONTENT</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>1.1</b> GruviMystics.com is a single and exclusive franchise-based - business to consumer (b2c) - online interactive live 
                                    streaming video chat website. 
                                </p>
                                <p class="text-indent"><b>1.2</b> The entertainment provided on the website is rendered by Adult individuals (spread worldwide), who are commonly 
                                    designated Chat Hosts.
                                </p>
                                <p class="text-indent"><b>1.3</b> ​ The chat host chat and carry out live webcam performance and workshops, in front of their camera, for subscribers all 
                                    over the world, who have selected them according to their area of interest, pictures, videos and free chat area available 
                                    for free on the website. 
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">2ND CLAUSE: GruviMystics.com - SERVICES AND WARRANTIES</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>2.1</b> GruviMystics.com provides the following online entertainment services:
                                </p>
                                <p class="text-indent"><b>2.1.1</b> Free and pay per minute live video chat;
                                </p>
                                <p class="text-indent"><b>2.2</b> As part of the services provided, there is the option to engage in a "Two Way Video Personal Consultation" session 
                                    with any Chat Host online. 
                                </p>
                                <p class="text-indent"><b>2.2</b> As part of the services provided, there is the option to engage in a "Two Way Video Personal Consultation" session 
                                    with any Chat Host online. 
                                </p>
                                <p class="text-indent"><b>2.3</b> The service mentioned in the previous clause, enables the subscriber to share his/her own camera's video feed with 
                                    the Chat Host. 
                                </p>
                                <p class="text-indent"><b>2.4</b> It is solely the subscriber's decision whether to enable the referred feature or remain faceless. 
                                </p>
                                <p class="text-indent"><b>2.5</b> ​ The subscriber to GruviMystics.com acknowledges and expressly agrees by accepting this Agreement that 
                                    GruviMystics.com may record the chat hosts' video streams, any video, chat or any other type of communication of the 
                                    subscriber on GruviMystics.com. 
                                </p>
                                <p class="text-indent"><b>2.6</b> To the extent permitted by law, GruviMystics.com makes no warranties or representations as to the information, 
                                    services or products provided through or in connection with the service. subscriber's use of the service is at his/her own 
                                    risk. 
                                </p>
                                <p class="text-indent"><b>2.7</b> ​GruviMystics.com makes no warranty of merchantability, fitness for any purpose, or non-results of the use of the 
                                    content in terms of their correctness, accuracy, timeliness, reliability or otherwise.
                                </p>
                                <p class="text-indent"><b>2.8</b> ​GruviMystics.com nor any party involved in creating, producing, or delivering the server or content is liable for any 
                                    direct, incidental, consequential, indirect or punitive damages arising from the access to, use of, or interpretation of, the 
                                    services, products or information provided by or through GruviMystics.com, without prejudice of the established in the 
                                    present agreement. 
                                </p>
                                <p class="text-indent"><b>2.9</b> The appearance of content on the website does not mean that GruviMystics.com supports the author or takes the 
                                    responsibility for such content. 
                                </p>
                                <p class="text-indent"> The Chat Hosts are acting as independent services providers and they may in no event be considered as employees of 
                                    GruviMystics.com, its agents or commissioners. 
                                </p>
                                <p class="text-indent"> Any services provided by the Chat Hosts will be the sole responsibility of the Chat Host and in no event the 
                                    recommendations advices provided by the Chat Hosts could be considered as the ones of GruviMystics.com. 
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">3RD CLAUSE: GruviMystics.com - PRINCIPLES</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>3.1</b> GruviMystics.com has no intention to support immoral interests, therefore it applies strict rules.
                                </p>
                                <p class="text-indent"><b>3.2</b> GruviMystics.com's services are only available for persons over the age of 18 (21 in some regions), or under the referred age, if legally emancipated. although, no sexually explicit material is intended within GruviMystics.com, this protection assures no sexually explicit materials are within the easy reach of minors.
                                </p>
                                <p class="text-indent"><b>3.3</b> Persons under the age of 18 (21 in some regions) are also not allowed to be chat hosts.
                                </p>
                                <p class="text-indent"><b>3.4</b> All Chat Hosts accept this agreement provided to them in an online form, which is proven sufficient for both parties and represent their free will to enter into a contractual relation in the specific terms referred therein.
                                </p>
                                <p class="text-indent"><b>3.5</b> The accounts of the chat hosts are immediately and permanently suspended if they violate GruviMystics.com's principles.
                                </p>
                                <p class="text-indent"><b>3.6</b> At GruviMystics.com there is a zero tolerance policy related to child pornography (written, audio or visual). in case of the slightest suspicion, the account in question is immediately and permanently closed and the appropriate authorities are contacted.
                                </p>
                                <p class="text-indent"><b>3.7</b> The GruviMystics.com support team undertakes all possible efforts to continuously check subscriber's information and chat logs for violations with the means made available.
                                </p>
                                <p class="text-indent"><b>3.8</b> GruviMystics.com reserves the right to apply immediate and permanent suspension in case a screen name is offensive, refers to minors or upon the slightest suspicion of forgery. 
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">4TH CLAUSE: SUBSCRIBERS</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>4.1</b> ​In order to subscribe GruviMystics.com it is mandatory to be at least 18 years old or 21 in some regions, or under the referred age, if legally emancipated, in compliance with the local regulations applicable to the subscriber.
                                </p>
                                <p class="text-indent"><b>4.2</b> ​By registering on GruviMystics.com and by accepting this Agreement, subscribers agree to indemnify, pay the costs of defense and hold harmless GruviMystics.com, its officers, directors, affiliates, attorneys, shareholders, managers, members, agents and employees from any and all claims, losses, liabilities or expenses (including reasonable attorneys' fees) brought by third parties arising out of or related to their conduct, statements or actions, as well as breach of any term, condition or promise contained herein and unlawful conduct in the framework of this Agreement.
                                </p>
                                <p class="text-indent"><b>4.3</b> ​In case of subscriber’s unlawful conduct or breach of the present Agreement, GruviMystics.com may terminate, without notice, the subscriber's account and/or anything associated with it. GruviMystics.com shall not be held responsible for any possible loss as a result of such termination, nor for any credit compensation or refund.
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">5TH CLAUSE: FEES AND UNLAWFUL CONDUCT</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>5.1</b> ​The Subscribers pay for accessing the Chat Hosts on GruviMystics.com on a per-minute basis at the available areas of entertainment outlined above in Clause 2. The amounts in question are subject to change at the discretion of GruviMystics.com without previous consent by the Subscribers. 
                                </p>
                                <p class="text-indent"><b>5.2</b> ​The option of "Readings" with the Chat Hosts is free. If the Subscribers wish to access premium features (such as Private reading), they must click on "Start a private reading" or "Consult now" to begin a private video chat with the Chat Host where their accounts are charged on a per-minute basis. 
                                </p>
                                <p class="text-indent"><b>5.3</b> ​The Subscribers may use the ‘Add Credits’ option during private readings with Chat Hosts. The use of this feature allows the Subscribers to immediately and manually top-up their credit balance with the package chosen during the reading. Subscribers are notified about the referred auto-purchase each time when it occurs and have the option to stop the next charge by clicking on the ‘X’ button on the notifying message. The use of any aforementioned options may change the default payment method previously set by the Subscribers.
                                </p>
                                <p class="text-indent"><b>5.4</b> ​Billing of a given account (the actual per-minute price paid for the chosen access) depends on the physical location where the account was created.
                                </p>
                                <p class="text-indent"><b>5.5</b> ​In case of a query, Gruvi Mystics is able to help regarding specific transactions made through different payment providers by contacting the provider in question. For billing information and support, the following should be contacted: Gruvi Mystics Support or by sending an email to <a href="mailto:info@gruvimystics.com">info@GruviMystics.com</a>. 
                                </p>
                                <p class="text-indent"><b>5.6</b> ​For bank related charges, Subscribers are advised to contact their banks directly.
                                </p>
                                <p class="text-indent"><b>5.7</b> ​GruviMystics.com does not take responsibility for any unforeseen difficulties occurring outside of GruviMystics.com.
                                </p>
                                <p class="text-indent"><b>5.8</b> ​Notwithstanding the referred clauses above, GruviMystics.com shall not be liable for any defamatory; offensive or illegal conduct by any Subscriber; or for any failure of performance, error, omission, interruption, deletion, defect, delay in operation or transmission; communications line failure; theft, destruction or unauthorized access; alteration of or use of records; whether under contract or through tort law, or under any other cause of action; for any amount over and above the amount paid by the Subscriber to GruviMystics.com. 
                                </p>
                                <p class="text-indent"><b>5.9</b> ​Under no circumstances, including but not limited to negligence, shall GruviMystics.com or any of its related, affiliated companies be liable for any direct, indirect, incidental, special, consequential or punitive damages that result from the use of, or the inability to use the service, without prejudice of the established in the present clause.
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">6TH CLAUSE: REFUND POLICY</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>6.1</b> ​Subscribers dissatisfied with the provided paid services, may contact Gruvi Mystics' Customer Service by sending a message from their registered email address to info@GruviMystics.com with a detailed description of complaint.
                                </p>
                                <p class="text-indent"><b>6.2</b> ​Customer Service accepts complaints sent within 24 hours of the incident. It carefully investigates all cases based on the available data and informs the Subscribers in a written message sent to their registered email address within 24 hours. 
                                </p>
                                <p class="text-indent"><b>6.3</b> ​Customer Service may offer compensations in cases where the Subscribers suffered a financial loss and only in the extent of credits spent in the given event. Claiming that a reading did not realize cannot be the base of any refunds. The Refund Policy covers paid services only and not the events happening afterwards; Gruvi Mystics does not have influence on all conceivable episodes occurring in the Subscribers’ lives. Credits received without actual payments (with coupons, gift cards, compensation, etc.) are not subject to refund. 
                                </p>
                                <p class="text-indent"><b>6.4</b> ​If technical or content related complaints arise due to Gruvi Mystics' or the Chat Hosts’ systems, then Customer Service may compensate the Subscribers in the extent of credits spent in a given event. If technical or content related incident occurs from the Subscribers’ side, then Customer Service cannot accept the Subscribers’ compensation request. Customer Service handles every case individually and makes all reasonable effort to resolve it amicably.
                                </p>
                                <p class="text-indent"><b>6.5</b> ​Based on our unique Money Back Guarantee, Customer Service may refund all credits spent in one single event per Subscriber. Customer Service handles every case individually and makes all reasonable effort to resolve it amicably. 
                                </p>
                                <p class="text-indent"><b>6.6</b> ​Customer Service refunds money to the Subscribers’ credit cards only in well-founded cases and only full credit packages to the credit card used on GruviMystics.com. When a credit package is refunded, its unused portion is deducted from the Subscribers’ GruviMystics.com account during the refund. Certain payment methods do not permit refunds to be executed because of technical reasons. In these cases we reserve the right to permanently close the Subscriber's account.
                                </p>
                                <p class="text-indent"><b>6.7</b> ​Dishonest behavior related to online transactions are handled as high priority cases, though the Subscriber's privacy is always respected during the handling of these matters. Nevertheless, such cases may be outsourced to entities specialized in this field. 
                                </p>
                                <p class="text-indent"><b>6.8</b> ​In case of fraudulent transactions, GruviMystics.com reserves the right to use all available information at its disposal during any kind of legal proceedings, including, and among others not listed here: browser history, IP and email addresses and any other traceable activity related to the incident. During such legal proceedings GruviMystics.com may involve other, professional investigating parties and share certain information in order to comply with federal law, vindicate its rights and represent the best interests of its Subscribers. 
                                </p>
                                <p class="text-indent"><b>6.9</b> ​GruviMystics.com grants full cooperation to legal authorities investigating fraudulent transactions and other matters falling under legal jurisdictions, as well as respond to subpoenas and court orders. 
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">7TH CLAUSE: SUBSCRIBER DECLARATIONS</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>7.1</b> ​I expressly authorize GruviMystics.com and/or any other entity under instructions of GruviMystics.com, to monitor, record and log all my online activities on these websites (including chat, video, e-mail). 
                                </p>
                                <p class="text-indent"><b>7.2</b> ​I acknowledge and agree that any material recorded or any original work made under this Agreement and/or when using GruviMystics.com services (and all rights therein, including, without limitation, copyright) belong to and shall be the sole and exclusive property of GruviMystics.com 
                                </p>
                                <p class="text-indent"><b>7.3</b> ​I hereby expressly assign, and transfer, without further compensation, to GruviMystics.com and/or to any other entity acting under the instructions of GruviMystics.com, the results, content, and proceeds of my appearance(s) (including all such appearances made to date) videos, audio, chat, dialogue, texts, acts, and instructional videos and advices, all of which are part of services provided - including all author rights to the above mentioned materials, renewals and extensions of such rights worldwide and throughout the whole validity period of such rights. GruviMystics.com or any other entity acting under its instrucitons shall be deemed the authors thereof for all purposes and owner of all rights, title and interest, of every kind and character for the period of the validity of such rights, including any extentions and renewals, throughout the universe. 
                                </p>
                                <p class="text-indent"><b>7.4</b> ​GruviMystics.com may use and reuse, publish, distribute, edit, excerpt, exhibit and otherwise exploit my name (real or fictional), likeness, persona, performance, voice, pictures, chat, video, audio, biological information and identification, and statements, for any and all uses, in whole or in part, in any and all media and manners now known or learned, for use throughout the universe, without limitation, including in connection with the advertising, exploitation and publicizing.
                                </p>
                                <p class="text-indent"><b>7.5</b> ​I will not give out any personal information. 
                                </p>
                                <p class="text-indent"><b>7.6</b> ​I hereby expressly waive any rights and declare to withdraw any claim, to the extent permitted by law that any use by GruviMystics.com violates any of my rights, including but not limited to moral rights, privacy rights, rights to publicity, proprietary or other rights, and/or rights to credit for the material or ideas set for therein. 
                                </p>
                                <p class="text-indent"><b>7.7</b> ​GruviMystics.com may edit my appearance as they see fit (and I waive any and all moral rights that I may have), and I understand that they have no obligation to use my appearance(s). 
                                </p>
                                <p class="text-indent"><b>7.8</b> ​Still pictures may be made from video or my appearance(s) by any means, and I grant to GruviMystics.com, its successors, licensees and assignees the right to use said photographs, without further payment to me in printed publications, digitally on the internet or via CD, or any other media, without restrictions.
                                </p>
                                <p class="text-indent"><b>7.9</b> ​I also grant to GruviMystics.com,its successors, licensees and assignees the right to use any photos taken by me (via webcam or by other means) and sent for publication on the site, without further payment to me in printed publications, digitally on the internet or via CD, or any other media, without restrictions. 
                                </p>
                                <p class="text-indent"><b>7.10</b> I hereby expressly waive any further financial compensation for any of the rights assigned, transferred or granted to GruviMystics.com under this agreement.
                                </p>
                                <p class="text-indent"><b>7.11</b> ​I declare and acknowledge that I am not acting on behalf of a legal person but as an individual consumer and in no event the purchase of services under this Agreement could be considered as being part of my professional activity. 
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">8TH CLAUSE: SUBSCRIBER DUTIES</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>8.1</b> ​I am fully liable for any false disclosures and responsible for any legal claims that may arise from viewing, reading, or downloading of material and images contained within this website. 
                                </p>
                                <p class="text-indent"><b>8.2</b> ​I will never expose minors to the content of the website and will take on full precautions to avoid any type of exhibition or access of the minors to the website, namely by not including the website in their list of favorite sites to visit. I will be solely reponsible in case any minor would access any information restricted for minors acces through my GruviMystics.com account or using my credit card details.
                                </p>
                                <p class="text-indent"><b>8.3</b> ​I assume full responsibility to maintain the security of my account and password.
                                </p>
                                <p class="text-indent"><b>8.4</b> ​I will not arrange personal appointments with any Chat Host, since it is prohibited.
                                </p>
                                <p class="text-indent"><b>8.5</b> ​I will not use obscene words, threaten or quarrel with, or violate the rights of visitors, chat hosts, GruviMystics.com support persons or management of the website, since it is prohibited. 
                                </p>
                                <p class="text-indent"><b>8.6</b> ​Text content sent or forwarded and the chosen user name will not be offensive, will not suggest pedophilia, adolescence, bestiality or zoophilia, or refer to elimination or consumption of any bodily waste.
                                </p>
                                <p class="text-indent"><b>8.7</b> ​I will not use remarks and user names that are unacceptable by the standards of good taste, suggesting violation of the law or deceiving others. 
                                </p>
                                <p class="text-indent"><b>8.8</b> ​I will inform, immediately, GruviMystics.com of any unlawful conduct of the chat hosts, as well as of any unlawful use of trademarks, brands and/or registered music. 
                                </p>
                                <p class="text-indent"><b>8.9</b> ​I will not solicit, purchase or sell any goods or enter into any business or deal with the Chat Hosts. 
                                </p>
                                <p class="text-indent"><b>8.10</b> I will not take any advice, recommendation or suggestion made by any Chat Hosts as a professional advice, screening any information given to me and acting on my own free will. 
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">9TH CLAUSE: SUBSCRIPTION CANCELLATION</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>9.1</b> ​The subscribers have the option to unsubscribe, at any time, from GruviMystics.com services.
                                </p>
                                <p class="text-indent"><b>9.2</b> ​The subscription cancellation can be accomplished by visiting GruviMystics.com 24/7 customer service center or by sending an e-mail to ​ info@GruviMystics.com​ . 
                                </p>
                                <p class="text-indent"><b>9.3</b> ​Once cancelled the account, the status of the Subscriber will be changed to "cancelled" and all the related details will be archived. 
                                </p>
                                <p class="text-indent"><b>9.4</b> ​GruviMystics.com reserves a right to suspend or cancel any subscription in case of breach of any term of this Agreement or any unlawful conduct of the Subscriber in the framework of this Agreement.
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">9TH CLAUSE: SUBSCRIPTION CANCELLATION</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>9.1</b> ​The subscribers have the option to unsubscribe, at any time, from GruviMystics.com services.
                                </p>
                                <p class="text-indent"><b>9.2</b> ​The subscription cancellation can be accomplished by visiting GruviMystics.com 24/7 customer service center or by sending an e-mail to ​ info@GruviMystics.com​ . 
                                </p>
                                <p class="text-indent"><b>9.3</b> ​Once cancelled the account, the status of the Subscriber will be changed to "cancelled" and all the related details will be archived. 
                                </p>
                                <p class="text-indent"><b>9.4</b> ​GruviMystics.com reserves a right to suspend or cancel any subscription in case of breach of any term of this Agreement or any unlawful conduct of the Subscriber in the framework of this Agreement.
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">10TH CLAUSE: PRIVACY POLICY AND PERSONAL DATA</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>10.1</b> I hereby authorize GruviMystics.com to process technical data related to my visit to the GruviMystics.com website and my data provided during the registration process in accordance with the present privacy policy, during the period of my subscription with GruviMystics.com and after cancellation of my subscription during the period necessary for GruviMystics.com to comply with legal requirements. 
                                </p>
                                <p class="text-indent"><b>10.2</b> I have not and will not provide any false information and/or documents to GruviMystics.com. therefore, i recognize that GruviMystics.com has the right to, immediately and unilaterally, terminate the present agreement upon the slightest suspicion of forgery. 
                                </p>
                                <p class="text-indent"><b>10.3</b> I also acknowledge the right of GruviMystics.com to be fully indemnified for all damages caused in case of my unlawful conduct or breach of the present contractual terms and conditions. 
                                </p>
                                <p class="text-indent"><b>10.4</b> I allow all my data to be inspected by GruviMystics.com, randomly, resorting to any existing means for such effect. notwithstanding, i am aware that it is not their duty to proceed with such inspection and said entities will not be considered responsible, either jointly or severally, in case of my unlawful conduct. 
                                </p>
                                <p class="text-indent"><b>10.5</b> I agree and authorise GruviMystics.com to obtain and store information automatically from my computer used to visit GruviMystics.com website (with use of cookies and similar technologies). GruviMystics.com may track the subscriber's visit to the website by giving a cookie when entering. Cookies help to collect anonymous data for tracking user trends and patterns. 
                                </p>
                                <p class="text-indent"><b>10.6</b> I agree and allow GruviMystics.com to collect, process and communicate to its processors, including the processors situated in countries not ensuring an adequate level of protection according to European Commission, thefollowing types of information of its Subscriber's: 
                                </p>
                                <p class="text-indent"><b>10.6.1</b> Information that the subscribers voluntarily provide to and/or authorize to view, such as names, email address, address, date of birth and other miscellaneous account information submitted through Gruvi Mystics Submission Forms.
                                </p>
                                <p class="text-indent"><b>10.6.2</b>​Number of visits and areas of GruviMystics.com pages visited by the subscriber's. software and hardware attributes might get logged too, along with any other data that can be gained from the general internet environment, such as browser type, ip address, etc.
                                </p>
                                <p class="text-indent"><b>10.6.3</b>​Private communications, such as telephone conversations, chat logs, faxes and letters to Gruvi Mystics staff along with e-mail messages to chat hosts or to our staff. GruviMystics.com also keeps chat hosts chat client logs for a limited period of time.
                                </p>
                                <p class="text-indent"><b>10.7</b>​GruviMystics.com will use the data collected from the subscriber's for mainly general purposes, such as improving services, contacting the subscriber's and customizing the website content and for promotional marketing services, to the extent allowed by law.
                                </p>
                                <p class="text-indent"><b>10.8</b>​GruviMystics.com may also research behavior patterns and trends to improve the subscriber's experience.
                                </p>
                                <p class="text-indent"><b>10.9</b>​GruviMystics.com takes serious security measures to grant maximum protection to information against unauthorized access, modification, disclosure or deletion of data. the subscriber's details are always protected by highly sophisticated security system.
                                </p>
                                <p class="text-indent"><b>10.10</b> GruviMystics.com guards the subscriber's information on a physical level as well as on electronic level.
                                </p>
                                <p class="text-indent"><b>10.11</b>​Besides using its own security software and mechanisms, GruviMystics.com also incorporates the most advanced security technologies available in order to ensure maximum safety of its subscribers.
                                </p>
                                <p class="text-indent"><b>10.12</b>​A non-exhaustive list of the referred technologies is as follows: sophisticated CAPTCHA system, SSL3 encryption, VeriSign and McAfee digital certificates.
                                </p>
                                <p class="text-indent"><b>10.13</b>​GruviMystics.com's system meets the security standards of pci dss, a standard set by visa/mastercard laying down stringent requirements via PayPal servicing. 
                                </p>
                                <p class="text-indent"><b>10.14</b>​GruviMystics.com employees' access to any personal information of GruviMystics.com subscribers is extremely limited and they are bound by confidentiality obligations. therefore, they might be subject to disciplinary measures, including the termination of their contracts and in serious cases even criminal prosecution should they fail to meet these strict obligations. 
                                </p>
                                <p class="text-indent"><b>10.15</b>​GruviMystics.com does not rent, sell, trade, share or otherwise transfer information to outside parties except the communication to GruviMystics.com services providers in order to ensure the good functioning of GruviMystics.com services. 
                                </p>
                                <p class="text-indent"><b>10.16</b>​The subscribers may obtain a copy of any personal information that GruviMystics.com process and ask for rectification of any uncorrect personal data, upon written request to the contacts provided in the present Agreement and indication of the address to which the information must be sent. If the subscriber would like to oppose the processing of personal data by GruviMystics.com, he/she is entitled to cancel his/her subscription on GruviMystics.com at any time as described above. 
                                </p>
                                <p class="text-indent"><b>10.17</b>​In case of any queries, the subscriber may use the contacts foreseen in clause 12 of the present Agreement.
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">11TH CLAUSE: GruviMystics.com - FUNCTIONALITY AND SECURITY</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>11.1</b> GruviMystics.com is a flash-technology based website which also uses the so-called "Shared-Object" technology in order to improve user experience. 
                                </p>
                                <p class="text-indent"><b>11.2</b> GruviMystics.com suggests the option "enable cookies" in the browser to ensure full functionality.
                                </p>
                                <p class="text-indent"><b>11.3</b> The GruviMystics.com support team monitors all camera feeds available on the website 24 hours a day, 7 days a week.
                                </p>
                                <p class="text-indent"><b>11.4</b> ​Due to its precise, unique design, the website has never had any serious security breach. 
                                </p>
                                <p class="text-indent"><b>11.5</b> ​​ GruviMystics.com is a scam-free zone.
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">12TH CLAUSE: SPAM </h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>12.1</b> The following is considered to constitute a spam: 
                                </p>
                                <p class="text-indent"><b>12.1.1</b> Manipulating messages, such as email headers, sent to or through GruviMystics.com computer systems in a way that may deceit subscribers of GruviMystics.com. 
                                </p>
                                <p class="text-indent"><b>12.1.2</b> Relaying email from a third party's mail servers without the permission of that third party. 
                                </p>
                                <p class="text-indent"><b>12.1.3</b> Sending, relaying or causing to be sent false, deceptive information or that is otherwise against the business interest of GruviMystics.com. 
                                </p>
                                <p class="text-indent"><b>12.1.4</b> Using or causing to be used GruviMystics.com computer systems to facilitate the transmission of unsolicited or unauthorized material. this includes any promotional materials, urls or any other form of unauthorized solicitation that you may upload, post, email, transmit, or otherwise make available. 
                                </p>
                                <p class="text-indent"><b>12.1.5</b> Uploading, posting, emailing, or transmitting the same message, URL, or post multiple times. 
                                </p>
                                <p class="text-indent"><b>12.1.6</b> Disrupting the normal flow of dialogue by posting messages in quick succession, multiple times, using capital letters only or otherwise acting in a manner that negatively affects other users' ability to engage in real-time exchanges. 
                                </p>
                                <p class="text-indent"><b>12.2</b> GruviMystics.com will not tolerate spam and distances itself from any actions related to spamming. 
                                </p>
                                <p class="text-indent"><b>12.3</b> Spamming through GruviMystics.com system or disturbing its subscribers is a violation of terms and conditions of the site. 
                                </p>
                                <p class="text-indent"><b>12.4</b> GruviMystics.com does everything in its power to protect its subscribers from deleterious effects of spamming. The use of all legal proceedings is considered in case of spamming inflicting a loss on GruviMystics.com. 
                                </p>
                                <p class="text-indent"><b>12.5</b> GruviMystics.com does not send spam messages. 
                                </p>
                                <p class="text-indent"><b>12.6</b> Notwithstanding the above, GruviMystics.com may send occasional promotional e-mails and every e-mail will contain the option to unsubscribe from the mailing list. 
                                </p>
                                <p class="text-indent"><b>12.7</b> Registered subscribers may occasionally receive newsletters in relation to GruviMystics.com. subscribing to and unsubscribing from these newsletters takes a single click. 
                                </p>
                                <p class="text-indent"><b>12.8</b> In case Subscribers would like to report spam, it is recommended the sending of an email from the "Contact" page, accessible from the footer of GruviMystics.com, using an online mail form. GruviMystics.com Support Team investigates all reports as soon as possible. 
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">13TH CLAUSE: CONTACTS </h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>13.1</b> Customer Service Department can be contacted from: 
                                </p>
                                <p class="text-indent"><b>13.1.1</b> Sending of an e-mail to <a href="mailto:info@gruvimystics.com">info@gruvimystics.com</a>​ , which can be sent to from the "Contact" page online or by any private e-mail client. 
                                </p>
                                <p class="text-indent"><b>13.2</b> GruviMystics.com grants subscribers from certain countries the possibility of using a toll-free phone number. 
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">14TH CLAUSE: MISCELLANEOUS  </h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>14.1</b> This agreement sets forth the full and complete understanding between subscribers and GruviMystics.com with respect to its subject matter, and supersedes all prior understanding or agreements, whether written or verbal. 
                                </p>
                                <p class="text-indent"><b>14.2</b> Unless contrary to law or otherwise stated, each provision of this Agreement shall survive termination. 
                                </p>
                                <p class="text-indent"><b>14.3</b> If any portion of this Agreement is deemed unenforceable by a Court of competent jurisdiction, it shall not affect the enforceability of the other portions of this Agreement. 
                                </p>
                                <p class="text-indent"><b>14.4</b> The prevailing party in any suit to enforce the terms hereof shall be entitled to recover his/her/its reasonable attorneys' fees.
                                </p>
                                <p class="text-indent"><b>14.5</b> This agreement may be modified upon notice by GruviMystics.com to its subscribers. In case of non-acceptance of said modifications, the subscribers may, immediately, proceed with the cancellation of their subscription within the terms established in the present agreement. If you do not cease using the GruviMystics.com website and its services, you will be conclusively deemed to have accepted the change. 
                                </p>
                                <p class="text-indent"><b>14.6</b> The English version of the present Agreement shall prevail for all legal effects. 
                                </p>
                                <p class="text-indent"><b>14.7</b> Only the english version shall prevail of all legal statements, statutory declarations made by GruviMystics.com. 
                                </p>
                                <p class="text-indent"><b>14.8</b> GruviMystics.com does not accept any kind of legal claims, or other complaints for the misunderstandings as a result of any mistranslations.
                                </p>
                                <p class="text-indent"><b>14.9</b> This Agreement and the relations arising out from it between GruviMystics.com and the subscribers will be governed by the law of The United States of America.
                                </p>
                                <p class="text-indent"><b>14.10</b> Any disputes arising between GruviMystics.com and the subscribers will be settled amicably and only when this solution is not efficient, the competent jurisdiction for the disputes arising from this Agreement will be the courts of the USofA. 
                                </p>
                            </div>
                        </div>

                        <h3 class="text-bold text-pink">15TH CLAUSE </h3>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-indent"><b>15.1</b> ​ If you do not want to receive marketing e-mails or other mails from us, please send an e-mail using your registered e-mail address to our Support team (see above contact details 12.1.1) with subject "unsubscribe".
                                </p>
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
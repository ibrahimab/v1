<?php

namespace Chalet\Mailer;

use Chalet\Encoder\EncoderInterface;

/**
 * @author  Jeroen Boschman <jeroen@webtastic.nl>
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class Mailer implements MailerInterface
{
    /**
     * @var string
     */
    const MULTIPART_HTML = 'text/html';

    /**
     * @var string
     */
    const MULTIPART_TEXT = 'text/plain';

    /**
     * @var array
     */
    private $body;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $replyTo;

    /**
     * @var string
     */
    private $bcc;

    /**
     * @var arry $to
     */
    private $to;

    /**
     * @var array
     */
    private $from;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $website;

    /**
     * @var array
     */
    private $websiteInfo;

    /**
     * @var string
     */
    private $path;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var array
     */
    private $signature;

    /**
     * @return void
     */
    public function __construct($defaultConfig = [])
    {
        $this->body    = [self::MULTIPART_HTML => '', self::MULTIPART_TEXT => ''];
        $this->config  = $defaultConfig;
        $this->replyTo = null;
        $this->bcc     = null;

        $this->to      = [

            'address'  => null,
            'name'     => null,
        ];

        $this->from    = [

            'address'  => null,
            'name'     => null,
        ];
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param string      $address
     * @param string|null $name
     */
    public function setTo($address, $name = null)
    {
        $this->to = [

            'address' => $address,
            'name'    => $name,
        ];
    }

    /**
     * @param string      $address
     * @param string|null $name
     */
    public function setFrom($address, $name = null)
    {
        $this->from = [

            'address' => $address,
            'name'    => $name,
        ];
    }

    /**
     * @param string $address
     */
    public function setReplyTo($address)
    {
        $this->replyTo = $address;
    }

    /**
     * @param string $bcc
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
    }

    /**
     * @param string $body
     * @param string $multipart
     */
    public function setBody($body, $multipart)
    {
        $this->body[$multipart] = $body;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @param array $websiteInfo
     */
    public function setWebsiteInfo(array $websiteInfo)
    {
        $this->websiteInfo = $websiteInfo;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param string $identifier
     * @param mixed  $value
     */
    public function setConfig($identifier, $value)
    {
        $this->config[$identifier] = $value;
    }

    /**
     * @param string $identifier
     * @param mixed  $default
     */
    public function getConfig($identifier, $default = null)
    {
        return (isset($this->config[$identifier]) ? $this->config[$identifier] : $default);
    }

    /**
     * @param EncoderInterface $encoder
     */
    public function setEncoder(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param string $identifier
     * @param string $signature
     */
    public function setSignature($identifier, $signature)
    {
        $this->signature = [

            'identifier' => $identifier,
            'signature'  => $signature,
        ];
    }

    /**
     * @return bool
     */
    public function send()
    {
        $header      = 'pic/mailheader/' . $this->website . '.jpg';
        $header_size = null;

        if (file_exists($this->path . $header)) {
            $header_size = getimagesize($this->path . $header);
        }

        $body = $this->body[self::MULTIPART_HTML];

        if (true === $this->getConfig('convert_to_html', false)) {

            if (true === $this->getConfig('make_clickable')) {
                $body = preg_replace('/([^=>\"]|^)(https?://[a-zA-Z0-9\./?&%=\-_\(#!\@]+)/', '\\1[link=\\2]\\2[/link]', $body);
            }

            $body = $this->encoder->encode($body);
            $body = nl2br($body);

            // convert [link=http://url/]tekst[/link]
            $body = preg_replace('/\[link=(https?[^]]+)\](.*)\[\/link\]/', '<a href="\\1">\\2</a>', $body);

            // [b] bold
            $body = preg_replace('/\[b\](.*)\[\/b\]/', '<b>\\1</b>', $body);

            // [i] italics
            $body = preg_replace('/\[i\](.*)\[\/i\]/', '<i>\\1</i>', $body);

            // [ul] u-list
            $body = preg_replace('/\[ul\](.*)\[\/ul\]/s', '<ul>\\1</ul>', $body);

            // [li] list-item
            $body = preg_replace('/\[li\](.*)\[\/li\]/', '<li style="margin-bottom:1.5em;">\\1</li>', $body);

            // clean up lists
            $body = str_replace('</li><br />' . "\n" . '<li>', '</li><li>', $body);
            $body = str_replace('</li><br />' . "\n" . '<li ', '</li><li ', $body);
        }

        if (preg_match('/\[' . $this->signature['identifier'] . '\]/', $body)) {
            $body = str_replace('[' . $this->signature['identifier'] . ']', $this->signature['signature'], $body);
        }

        $mail = new \wt_mail;

        # header-attachment toevoegen
        if (false === $this->getConfig('no_header_image', false)) {
            $cid = $mail->attachment($this->path . $header, 'image/jpeg', true);
        }

        // add attachments
        if (null !== ($attachments = $this->getConfig('attachments'))) {

            foreach ($attachments as $key => $value) {
                $mail->attachment($key, '', false, $value);
            }
        }

        // from
        if (null !== $this->from['address']) {
            $mail->from = $this->from['address'];
        } else {
            $mail->from = $this->websiteInfo['email'][$this->website];
        }

        // return-path
        if (null !== $this->website && isset($this->websiteInfo['email'][$this->website])) {
            $mail->returnpath = $this->websiteInfo['email'][$this->website];
        }

        // fromname
        if (null !== $this->from['name']) {
            $mail->fromname = $this->from['name'];
        } else {
            $mail->fromname = $this->websiteInfo['websitenaam'][$this->website];
        }

        // reply-to
        if (null !== $this->replyTo) {
            $mail->replyto = $this->replyTo;
        }

        // subject
        $mail->subject = $this->subject;

        // to
        $mail->to = $this->to['address'];

        // toname
        if (null !== $this->to['name']) {
            $mail->toname = $this->to['name'];
        }

        // bcc versturen?
        if (null !== $this->bcc) {
            $mail->bcc = $this->bcc;
        }

        $mail->plaintext = ''; # deze leeg laten bij een opmaak-mailtje
        $mail->html_top  = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">' . "\n" . '<html><head><meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/><style><!--' . "\n" . 'a:hover { color:#888888; }'. "\n" . '--></style>' . "\n" . '</head><body bgcolor="#ffffff" style="background-color:#ffffff;margin:0;padding:0;"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" style="background-color:#ffffff;width:100%;"><tr><td align="center" width="100%" style="background-color:#ffffff;width:100%;"><br><table align="center" border="0" cellpadding="0" cellspacing="0" width="681" style="background-color:#ffffff;"><tr><td>';

        # topfoto
        if (true === $this->getConfig('no_header_image', false)) {

            $mail->html_top .= '&nbsp;';

        } else {

            $mail->html_top .= '<a href="' . $this->encoder->encode($this->websiteInfo['basehref'][$this->website] . $this->getConfig('add_to_basehref', '')) . '">';

            if ($cid) {
                $mail->html_top .= '<img src="cid:' . $cid . '" ' . $header_size[3] . ' alt="' . $this->encoder->encode($this->websiteInfo['websitenaam'][$website]) . '" border="0">';
            } else {
                $mail->html_top .= '<img src="' . $this->encoder->encode($this->websiteInfo['basehref'][$this->website]) . $header . '" ' . $header_size[3] . ' alt="' . $this->encoder->encode($this->websiteInfo['websitenaam'][$this->website]) . '" border="0">';
            }

            $mail->html_top .= '</a><br/>&nbsp;';
        }

        $mail->html_top    .= '</td></tr><tr><td style="font-family: Verdana, Helvetica, Arial, sans-serif;line-height: 14pt;font-size: 10pt;padding-top:10px;">' . "\n";
        $mail->html_bottom  = '<br/>&nbsp;</td></tr></table></td></tr></table></body></html>' . "\n";
        $mail->html         = $body;

        return $mail->send();
    }
}
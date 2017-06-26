<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="format-detection" content="telephone=no">
<meta name="format-detection" content="date=no">
<meta name="format-detection" content="address=no">
<meta name="format-detection" content="email=no">
<title>{title}</title>
<link href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet" type="text/css">
<style type="text/css">
/**
 * SimpleApp
 * http://notification-emails.com/
 * Last Modified: 10/05/2016
**/
/* Reset */
body { Margin: 0; padding: 0; min-width: 100%; }
a, #outlook a { display: inline-block; }
a, a span { text-decoration: none; }
img { line-height: 1; outline: none; border: 0; text-decoration: none; -ms-interpolation-mode: bicubic; mso-line-height-rule: exactly; }
table { border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
td { padding: 0; }
/* Email preview text */
.email_summary { display:none; font-size:1px; line-height:1px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; }
/* Typography */
.font_default, h1, h2, h3, h4, h5, h6, p, a { font-family: Helvetica, Arial, sans-serif; /* this is used for email clients that don't support webfonts */ }
small { font-size: 86%; font-weight: normal; }
.pricing_box_cell small { font-size: 74%; }
.font_default, p { font-size: 15px; /* default text size */ }
p { line-height: 23px; Margin-top: 16px; Margin-bottom: 24px; }
.lead { font-size: 19px; line-height: 27px; Margin-bottom: 16px; }
.header_cell .column_cell { font-size: 20px; font-weight: bold; }
.header_cell p { margin-bottom: 0; }
h1, h2, h3, h4, h5, h6 { Margin-left: 0; Margin-right: 0; Margin-top: 16px; Margin-bottom: 8px; padding: 0; }
.line-through { text-decoration: line-through; }
h1,
h2 {
	font-size: 26px;
	line-height: 36px;
	font-weight: bold;
}
.pricing_box h1,
.pricing_box h2,
.primary_pricing_box h1,
.primary_pricing_box h2 {
	line-height: 20px;
	Margin-top: 16px;
	Margin-bottom: 0;
}
h3,
h4 {
	font-size: 22px;
	line-height: 30px;
	font-weight: bold;
}
h5 {
	font-size: 18px;
	line-height: 26px;
	font-weight: bold;
}
h6 {
	font-size: 16px;
	line-height: 24px;
	font-weight: bold;
}
.primary_btn td,
.secondary_btn td {
	font-size: 16px;
	mso-line-height-rule: exactly;
}
.primary_btn a,
.secondary_btn a {
	font-weight: bold;
}
/* Grid */
.email_body {
	padding: 32px 10px;
	text-align: center;
}
.email_container, .row, .col-1, .col-13, .col-2, .col-3 {
	display: inline-block;
	width: 100%;
	vertical-align: top;
	text-align: center;
}
.email_container {
	width: 100%;
	margin: 0 auto;
}
.email_container,
.row,
.col-3 { 
	max-width: 580px; 
}
.col-1 { max-width: 190px; }
.col-2 { max-width: 290px; }
.col-13 { max-width: 390px; }

.row { margin: 0 auto; }
.column {
	width: 100%;
	vertical-align: top;
}
.column_cell {
	padding: 16px;
	text-align: center;
	vertical-align: top;
}
.col-bottom-0 .column_cell { padding-bottom: 0; }
.col-top-0 .column_cell { padding-top: 0; }
/* Content Blocks */
.email_container, 
.header_cell, 
.jumbotron_cell, 
.content_cell, 
.footer_cell,
.image_responsive {
	font-size: 0 !important;
	text-align: center;
}
/* Header & Footer */
.header_cell,
.footer_cell {
	padding-bottom: 16px;
}
.header_cell .column_cell,
.footer_cell .col-13 .column_cell,
.footer_cell .col-1 .column_cell {
	text-align: left;
	padding-top: 16px;
}
.header_cell {
	-webkit-border-radius: 4px 4px 0 0;
	border-radius: 4px 4px 0 0;
}
.header_cell img {
	max-width: 156px;
	height: auto;
}
.footer_cell {
	text-align: center;
	-webkit-border-radius: 0 0 4px 4px;
	border-radius: 0 0 4px 4px;
}
.footer_cell p { Margin: 16px 0; }
/* Jumbotron */
.invoice_cell .column_cell {
	text-align: left;
	padding-top: 0;
	padding-bottom: 0;
}
.invoice_cell p {
	margin-top: 8px;
	margin-bottom: 16px;
}
/* Content */
.pricing_box {
	border-collapse: separate;
	padding: 10px 16px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
}
.primary_pricing_box {
	border-collapse: separate;
	padding: 18px 16px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
}
.text_quote .column_cell {
	border-left: 4px solid;
	text-align: left;
	padding-right: 0;
	padding-top: 0;
	padding-bottom: 0;
}
/* Buttons */
.primary_btn,
.secondary_btn {
	clear: both;
	margin: 0 auto;
}
.primary_btn td,
.secondary_btn td {
	text-align: center;
	vertical-align: middle;
	padding: 12px 24px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
}
.primary_btn a,
.primary_btn span,
.secondary_btn a,
.secondary_btn span {
	text-align: center;
	display: block;
}
.label .font_default {
	font-size: 10px;
	font-weight: bold;
	text-transform: uppercase;
	letter-spacing: 2px;
	padding: 3px 7px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	white-space: nowrap;
}
/* Icon Holder + Rules */
.icon_holder, .hruler {
	width: 62px;
	margin-left: auto;
	margin-right: auto;
	clear: both;
}
.icon_holder { width: 48px; }
.hspace, .hruler_cell {
	font-size: 0;
	height: 8px;
	overflow: hidden;
}
.hruler_cell {
	height: 4px;
	line-height: 4px;
}
.icon_cell {
  font-size: 0;
  line-height: 1;
  -webkit-border-radius: 80px;
  border-radius: 80px;
  padding: 8px;
  height: 48px;
}
/* Product Row */
.product_row { padding: 0 0 16px; }
.product_row .column_cell { padding: 16px 16px 0; }
.image_thumb img {
	-webkit-border-radius: 4px;
	border-radius: 4px;
}
.product_row .col-13 .column_cell { text-align: left; }
.product_row h6 { Margin-top: 0; }
.product_row p {
	Margin-top: 8px;
	Margin-bottom: 8px;
}
.order_total_right .column_cell { text-align: right; }
.order_total_left .column_cell { text-align: left; }
.order_total p { Margin: 8px 0; }
.order_total h2 { Margin: 8px 0; }
/* Responsive Images */
.image_responsive img {
	display: block;
	width: 100%;
	height: auto;
	max-width: 580px;
	margin-left: auto;
	margin-right: auto;
}
/* Colors */
body,
.email_body {
	background-color: #d8dde4; 
}
.content_cell {
	background-color: #ffffff; 
}
.header_cell,
.secondary_btn td,
.icon_primary .icon_cell,
.primary_pricing_box {
	background-color: #2f68b4;
}
.jumbotron_cell,
.footer_cell,
.pricing_box {
	background-color: #f2f2f5;
}
.primary_btn td,
.label .font_default {
	background-color: #22aaa0;
}
.icon_secondary .icon_cell {
	background-color: #e1e3e7;
}
.label_1 .font_default {
	background-color: #62a9dd;
}
.label_2 .font_default {
	background-color: #8965ad;
}
.label_3 .font_default {
	background-color: #df6164;
}
.header_cell .column_cell,
.header_cell a,
.header_cell a span,
.primary_btn a,
.primary_btn span,
.secondary_btn a,
.secondary_btn span,
.label .font_default,
.primary_pricing_box,
.primary_pricing_box h1,
.primary_pricing_box small {
	color: #ffffff;
}
h2,
h4,
h5,
h6 {
	color: #383d42;
}
.column_cell {
	color: #888888;
}
h1,
h3,
a,
a span,
.text-secondary,
.column_cell .text-secondary, 
.content_cell h2 .text-secondary {
	color: #2f68b4;
}
.footer_cell a, 
.footer_cell a span {
	color: #7a7a7a;
}
.text-muted,
.footer_cell .column_cell, 
.content h4 span, 
.content h3 span {
	color: #b3b3b5;
}
.footer_cell,
.product_row, 
.order_total {
	border-top: 1px solid;
}
.product_row, 
.order_total,
.icon_secondary .icon_cell, 
.footer_cell, 
.content .product_row, 
.content .order_total,
.pricing_box,
.text_quote .column_cell {
	border-color: #d8dde4;
}
/* Responsive */
@media screen {
  h1, h2, h3, h4, h5, h6, p, a, .font_default {
	  font-family: "Noto Sans", Helvetica, Arial, sans-serif !important;  /* web font */
  }
  .primary_btn td, .secondary_btn td {
	  padding: 0 !important;
  }
  .primary_btn a, .secondary_btn a {
	  padding: 12px 24px !important;
  }
}
@media screen and (min-width: 631px) and (max-width: 769px) {
.col-1, .col-2, .col-3, .col-13 {
	float: left !important;
}
.col-1 {
	width: 200px !important;
}
.col-2 {
	width: 300px !important;
}
}
@media screen and (max-width: 630px) {
  .jumbotron_cell {
	  background-size: cover !important;
  }
  .row, .col-1, .col-13, .col-2, .col-3 {
	  max-width: 100% !important;
  }
}
</style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="margin: 0;padding: 0;min-width: 100%;background-color: #d8dde4;">
<div class="email_body" style="padding: 32px 10px;text-align: center;background-color: #d8dde4;">
  	<div class="email_container" style="display: inline-block;width: 100%;vertical-align: top;text-align: center;margin: 0 auto;max-width: 580px;font-size: 0 !important;">
      <table class="header" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;">
        <tbody>
          <tr>
            <td class="header_cell col-bottom-0" align="center" valign="top" style="padding: 0;text-align: center;padding-bottom: 16px;-webkit-border-radius: 4px 4px 0 0;border-radius: 4px 4px 0 0;background-color: #2f68b4;font-size: 0 !important;">
                <div class="row" style="display: inline-block;width: 100%;vertical-align: top;text-align: center;max-width: 580px;margin: 0 auto;">
                  <div class="col-3" style="display: inline-block;width: 100%;vertical-align: top;text-align: center;max-width: 580px;">
                    <table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;width: 100%;vertical-align: top;">
                      <tbody>
                        <tr>
                          <td class="column_cell font_default" align="center" valign="top" style="padding: 16px;font-family: Helvetica, Arial, sans-serif;font-size: 20px;text-align: left;vertical-align: top;color: #ffffff;font-weight: bold;padding-bottom: 0;padding-top: 16px;">{title}</td><!-- /.column_cell -->
                        </tr>
                      </tbody>
                    </table><!-- /.column -->
                  </div><!-- /.col-3 -->
                </div><!-- /.row -->
            </td><!-- /.header_cell -->
          </tr>
        </tbody>
      </table><!-- /.header -->
      <table class="jumbotron" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;">
        <tbody>
          <tr>
            <td class="jumbotron_cell" align="center" valign="top" style="padding: 0;text-align: center;background-color: #f2f2f5;font-size: 0 !important;">
                <div class="row" style="display: inline-block;width: 100%;vertical-align: top;text-align: center;max-width: 580px;margin: 0 auto;">
                </div><!-- /.row -->
            </td><!-- /.jumbotron_cell -->
          </tr>
        </tbody>
      </table><!-- /.jumbotron -->
      <table class="content" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;">
        <tbody>
          <tr>
            <td class="content_cell" align="center" valign="top" style="padding: 0;text-align: center;background-color: #ffffff;font-size: 0 !important;">
                <div class="row" style="display: inline-block;width: 100%;vertical-align: top;text-align: center;max-width: 580px;margin: 0 auto;">
                  <div class="col-3" style="display: inline-block;width: 100%;vertical-align: top;text-align: center;max-width: 580px;">
                    <table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;width: 100%;vertical-align: top;">
                      <tbody>
                        <tr>
                          <td class="column_cell font_default" align="center" valign="top" style="padding: 16px;font-family: Helvetica, Arial, sans-serif;font-size: 15px;text-align: left;vertical-align: top;color: #888888;">
                              <p style="font-family: Helvetica, Arial, sans-serif;font-size: 15px;line-height: 23px;margin-top: 16px;margin-bottom: 24px;">{content}</p>
                          </td><!-- /.column_cell -->
                        </tr>
                      </tbody>
                    </table><!-- /.column -->
                  </div><!-- /.col-3 -->
                </div><!-- /.row -->
            </td><!-- /.content_cell -->
          </tr>
        </tbody>
      </table><!-- /.content -->
      <table class="footer" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;">
        <tbody>
          <tr>
            <td class="footer_cell" align="center" valign="top" style="padding: 0;text-align: center;padding-bottom: 16px;-webkit-border-radius: 0 0 4px 4px;border-radius: 0 0 4px 4px;background-color: #f2f2f5;border-top: 1px solid;border-color: #d8dde4;font-size: 0 !important;">
                <div class="row" style="display: inline-block;width: 100%;vertical-align: top;text-align: center;max-width: 580px;margin: 0 auto;">
                  <div class="col-13 col-bottom-0" style="display: inline-block;width: 100%;vertical-align: top;text-align: center;">
                    <table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;width: 100%;vertical-align: top;">
                      <tbody>
                        <tr>
                          <td class="column_cell font_default" align="center" valign="top" style="padding: 16px;font-family: Helvetica, Arial, sans-serif;font-size: 15px;text-align: center;vertical-align: top;color: #b3b3b5;padding-bottom: 0;padding-top: 16px;">
                          {institution_name}<br />
                          {address}
                          </td><!-- /.column_cell -->
                        </tr>
                      </tbody>
                    </table><!-- /.column -->
                  </div><!-- /.col-13 -->
                </div><!-- /.row -->
            </td><!-- /.footer_cell -->
          </tr>
        </tbody>
      </table><!-- /.footer -->
    </div><!-- /.email_container -->
</div><!-- /.email_body --> 
</body>
</html>
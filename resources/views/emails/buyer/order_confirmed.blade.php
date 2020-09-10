<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>DD | NEW Order -  #{{ $order->order_number }}</title>
</head>
<body>
	<blockquote class="gmail_quote" style="margin:0px 0px 0px 0.8ex;border-left:1px solid rgb(204,204,204);padding-left:1ex"><u></u>
    <div>
        <div style="margin:0px auto;max-width:854px">
            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:854px">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:0px 34.1563px;text-align:center;vertical-align:top">
                            <div style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:785.688px">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align:top;padding:0px">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="center" style="font-size:0px;padding:100px 0px 10px;word-break:break-word">
                                                                <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td style="width:149px">
                                                                                <div><img width="150px" src="{{ asset($logo) }}">
                                                                                    <br>
                                                                                </div>
                                                                                <br>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"  style="font-size:0px;padding:0px;word-break:break-word">
                                                                <div style="font-weight:bold;font-family:Arial;color:rgb(0,0,0);font-size:20px;line-height:20px;margin-top:50px">Order Confirmation</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"  style="font-size:0px;padding:23px 0px 62px;word-break:break-word">
                                                                <div style="text-transform:uppercase;font-family:Arial;font-size:14px;line-height:20px;color:rgb(0,0,0)">ORDER NO. {{ $order->order_number }}</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td  style="font-size:0px;padding:0px;word-break:break-word">
                                                                <div style="font-family:Arial;font-size:14px;line-height:20px;color:rgb(0,0,0)">
                                                                    <p style="margin:0px 0px 18px;text-transform:uppercase; text-align:center;">WE WILL SEND YOU AN EMAIL TO ACCESS THE TRACKING</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:0px;padding:20px 0px 0px;word-break:break-word">
                                                                <p style="border-top:1px solid rgb(221,221,221);margin:0px auto;width:785px"></p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin:0px auto;max-width:854px">
            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:854px">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:0px 34.1563px;text-align:center;vertical-align:top">
                            <div  style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:785.688px">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align:top;padding:0px">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="center"  style="font-size:0px;padding:0px 0px 47px;word-break:break-word">
                                                                <div style="font-weight:bold;font-family:Arial;color:rgb(0,0,0);font-size:20px;line-height:20px;margin-top:50px">Delivery</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="font-size:0px;padding:0px;word-break:break-word">
                                                                <div style="margin-bottom:23px;font-weight:bold;text-transform:uppercase;font-family:Arial;font-size:14px;color:rgb(0,0,0);line-height:19px">
                                                                    <div>STANDARD HOME SHIPPING</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"  style="font-size:0px;padding:0px;word-break:break-word">
                                                                <div style="max-width:358px;font-family:Arial;font-size:14px;color:rgb(0,0,0);line-height:19px"></div>
                                                            </td>
                                                        </tr>
                                                        @if(!empty( $order->shipping_address))
                                                        <tr>
                                                            <td align="center"  style="font-size:0px;padding:0px;word-break:break-word">
                                                                <div style="max-width:358px;font-family:Arial;font-size:14px;color:rgb(0,0,0);line-height:19px">{{ $order->shipping_address }}&nbsp;
                                                                    <br>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                        @if(!empty( $order->shipping_state ))
                                                        <tr>
                                                            <td align="center"  style="font-size:0px;padding:0px;word-break:break-word">
                                                                <div style="max-width:358px;font-family:Arial;font-size:14px;color:rgb(0,0,0);line-height:19px">{{ $order->shipping_state }}</div>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                        <tr>
                                                            <td align="center"  style="font-size:0px;padding:0px;word-break:break-word">
                                                                <div style="font-family:Arial;font-size:14px;line-height:20px;color:rgb(0,0,0)"></div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:0px;word-break:break-word">
                                                                <div style="height:50px">&nbsp;</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:0px;padding:0px;word-break:break-word">
                                                                <p style="border-top:1px solid rgb(221,221,221);margin:0px auto;width:785px"></p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin:0px auto;max-width:854px">
            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:854px">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:0px 34.1563px;text-align:center;vertical-align:top">
                            <div  style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:785.688px">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align:top;padding:0px">
                                                <br>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin:0px auto;max-width:854px">
            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:854px">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:0px 34.1563px;text-align:center;vertical-align:top">
                            <div  style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:785.688px">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align:top;padding:0px">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%"></table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin:0px auto;max-width:854px">
            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:854px">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:0px 34.1563px;text-align:center;vertical-align:top">
                            <div  style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align:top;padding:0px">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="center"  style="font-size:0px;padding:0px 0px 23px;word-break:break-word">
                                                                <div style="font-weight:bold;font-family:Arial;color:rgb(0,0,0);font-size:20px;line-height:20px;margin-top:50px">Products</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"  style="font-size:0px;padding:0px 0px 50px;word-break:break-word">
                                                                <div style="text-transform:uppercase;font-family:Arial;font-size:14px;line-height:20px;color:rgb(0,0,0)">{{ $order_item }}  ITEMs</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</blockquote>
    <table border="0" cellpadding="0" cellspacing="0" width="100%"
        style="font-family:Roboto,RobotoDraft,Helvetica,Arial,sans-serif;font-size:13px">
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <?php
                    $thumb = null;

                    for($i=0; $i < sizeof($item->item->images); $i++) {
                        if ($item->item->images[$i]->color != null) {
                            if ($item->item->images[$i]->color->name == $item->color) {
                                $thumb = $item->item->images[$i];
                                break;
                            }
                        }
                    }
                    ?>
                    @if ($thumb)
                    <img height="auto" src="{{ asset($thumb->list_image_path) }}" width="240" style="outline:none;object-fit:contain;border:0px;display:block;height:auto;width:240px; margin-left: auto; margin-right: auto;" tabindex="0">
                    @endif
                    <div class="a6S" style="text-align: center; margin-top: 50px;">
                        <div style="font-family:Arial;font-size:14px;line-height:20px;text-transform:uppercase;color:rgb(0,0,0)">
                            {{ $item->item->name }}
                        </div>
                        <div style="font-family:Arial;font-size:14px;line-height:20px;text-transform:uppercase">
                            <b style="background-color:rgb(255,255,255)">
                                <font color="#ff0000">(CONFIRM ORDERED)</font>
                            </b>
                        </div>
                        <div style="font-family:Arial;font-size:14px;line-height:20px;color:rgb(127,127,127)">{{ $item->color }}</div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
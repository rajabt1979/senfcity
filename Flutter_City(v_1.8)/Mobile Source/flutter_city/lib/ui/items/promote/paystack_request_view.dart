import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
// import 'package:flutter_paystack/flutter_paystack.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/provider/promotion/item_promotion_provider.dart';
import 'package:fluttercity/provider/user/user_provider.dart';
import 'package:fluttercity/repository/user_repository.dart';
import 'package:fluttercity/ui/common/dialog/error_dialog.dart';
import 'package:fluttercity/ui/common/dialog/success_dialog.dart';
import 'package:fluttercity/ui/common/dialog/warning_dialog_view.dart';
import 'package:fluttercity/ui/common/ps_button_widget.dart';
import 'package:fluttercity/ui/common/ps_textfield_widget.dart';
import 'package:fluttercity/utils/ps_progress_dialog.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/holder/item_paid_history_parameter_holder.dart';
import 'package:fluttercity/viewobject/holder/paystack_intent_holder.dart';
import 'package:fluttercity/viewobject/item.dart';
import 'package:fluttercity/viewobject/item_paid_history.dart';
import 'package:provider/provider.dart';

class PayStackRequestView extends StatefulWidget {
  const PayStackRequestView({
    Key? key,
    required this.item,
    required this.amount,
    required this.howManyDay,
    required this.stripePublishableKey,
    required this.paymentMethod,
    required this.startDate,
    required this.payStackKey,
    required this.startTimeStamp,
    required this.userProvider,
    required this.itemPaidHistoryProvider,
    required this.userEmail,
  }) : super(key: key);

  final Item? item;
  final String? amount;
  final String? howManyDay;
  final String? stripePublishableKey;
  final String? paymentMethod;
  final String? startDate;
  final String? payStackKey;
  final String? startTimeStamp;
  final UserProvider? userProvider;
  final ItemPromotionProvider? itemPaidHistoryProvider;
  final String? userEmail;

  @override
  State<StatefulWidget> createState() {
    return PayStackRequestViewState();
  }
}

class PayStackRequestViewState extends State<PayStackRequestView> {
  final TextEditingController emailController = TextEditingController();
  UserRepository? repo1;
  PsValueHolder? psValueHolder;
  bool bindDataFirstTime = true;

  //final PaystackPlugin plugin = PaystackPlugin();

  @override
  void initState() {
   // plugin.initialize(publicKey: widget.payStackKey!);
    // MpesaFlutterPlugin.setConsumerKey('JyDkh1nIueVAP9dt0BA6onbVPmtdNQ5e');
    // MpesaFlutterPlugin.setConsumerSecret('mnifM6PEGYYlOXat');
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    repo1 = Provider.of<UserRepository>(context);
    psValueHolder = Provider.of<PsValueHolder?>(context);

    return ChangeNotifierProvider<UserProvider>(
      lazy: false,
      create: (BuildContext context) {
        final UserProvider provider =
            UserProvider(repo: repo1, psValueHolder: psValueHolder);
        provider.getUser(provider.psValueHolder!.loginUserId!);
        return provider;
      },
      child: Consumer<UserProvider>(
          builder: (BuildContext context, UserProvider provider, Widget? child) {
        if (
          //provider != null &&
           // provider.user != null &&
            provider.user.data != null) {
          if (bindDataFirstTime) {
            emailController.text = provider.user.data!.userEmail!;
          }
        }
        return Scaffold(
          appBar: AppBar(
            backgroundColor: PsColors.mainColor,
            systemOverlayStyle: SystemUiOverlayStyle(
              statusBarIconBrightness: Utils.getBrightnessForAppBar(context),
            ),
            iconTheme:
                Theme.of(context).iconTheme.copyWith(color: PsColors.white),
            title: Text(
              Utils.getString('item_promote__pay_stack'),
              textAlign: TextAlign.center,
              style: Theme.of(context)
                  .textTheme
                  .headline6!
                  .copyWith(fontWeight: FontWeight.bold, color: PsColors.white),
            ),
            elevation: 0,
          ),
          body: SingleChildScrollView(
            child: Container(
              color: PsColors.backgroundColor,
              padding: const EdgeInsets.only(
                  left: PsDimens.space8, right: PsDimens.space8),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  const SizedBox(
                    height: PsDimens.space16,
                  ),
                  PsTextFieldWidget(
                      titleText: Utils.getString('item_entry__email'),
                      textAboutMe: false,
                      keyboardType: TextInputType.emailAddress,
                      hintText: 'Example@gmail.com',
                      textEditingController: emailController),
                  const SizedBox(
                    height: PsDimens.space12,
                  ),
                  Container(
                    margin: const EdgeInsets.only(
                        left: PsDimens.space12, right: PsDimens.space12),
                    child: PSButtonWidget(
                      hasShadow: true,
                      width: double.infinity,
                      titleText: Utils.getString('credit_card__pay'),
                      onPressed: () async {
                        if (emailController.text == '') {
                          showDialog<dynamic>(
                              context: context,
                              builder: (BuildContext context) {
                                return ErrorDialog(
                                  message: Utils.getString(
                                      'edit_profile__email_error'),
                                );
                              });
                        } else {
                          final DateTime dateTime = DateTime.now();
                          final int resultStartTimeStamp =
                              Utils.getTimeStampDividedByOneThousand(dateTime);
                          Navigator.pushNamed(
                              context, RoutePaths.payStackPayment,
                              arguments: PayStackInterntHolder(
                                item: widget.item,
                                amount: widget.amount,
                                howManyDay: widget.howManyDay,
                                paymentMethod: PsConst.PAYMENT_PAY_STACK_METHOD,
                                stripePublishableKey:
                                    widget.stripePublishableKey,
                                startDate: widget.startDate,
                                startTimeStamp: resultStartTimeStamp.toString(),
                                itemPaidHistoryProvider:
                                    widget.itemPaidHistoryProvider,
                                userProvider: widget.userProvider,
                                payStackKey: widget.payStackKey,
                                userEmail: emailController.text,
                              ));
                        }
                      },
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      }),
    );
  }
}

Future<void> startCheckoutWithPayStack(
    {String? userEmail,
    String? amount,
    ItemPromotionProvider?provider,
    Item? item,
    BuildContext? context,
    String? howManyDay,
    String? paymentMethod,
    String? startDate,
    String? startTimeStamp,
    String? payStackKey,
    String? token}) async {
  //Preferably expect 'dynamic', response type varies a lot!
  dynamic transactionInitialisation;
  //Better wrap in a try-catch for lots of reasons.

  try {
    //Run it
    // var mpesa = Mpesa(
    //   clientKey: "gVSDHWmRGai9zWjOxWS0HRkjcFGwl88b",
    //   clientSecret: "kmtlKNRQNareBpqj",
    //   passKey: "YOUR_LNM_PASS_KEY_HERE",
    //   initiatorPassword: "YOUR_SECURITY_CREDENTIAL_HERE",
    //   environment: "sandbox",
    // );

    await PsProgressDialog.showDialog(context!);

    if (transactionInitialisation['MerchantRequestID'] != null &&
        transactionInitialisation['CheckoutRequestID'] != null &&
        transactionInitialisation['ResponseCode'] != null &&
        transactionInitialisation['ResponseDescription'] != null &&
        transactionInitialisation['CustomerMessage'] != null) {
      print('ارسال موفق');

      // final DateTime dateTime = DateTime.now();

      // final int reultStartTimeStamp =
      //     Utils.getTimeStampDividedByOneThousand(dateTime);

      final ItemPaidHistoryParameterHolder itemPaidHistoryParameterHolder =
          ItemPaidHistoryParameterHolder(
              itemId: item!.id,
              amount: amount,
              howManyDay: howManyDay,
              paymentMethod: paymentMethod,
              paymentMethodNounce: Platform.isIOS ? token : token,
              startDate: startDate,
              startTimeStamp: startTimeStamp,
              razorId: '',
              purchasedId: '',
              isPayStack: PsConst.ONE);

      final PsResource<ItemPaidHistory> paidHistoryData = await provider!
          .postItemHistoryEntry(itemPaidHistoryParameterHolder.toMap());

      PsProgressDialog.dismissDialog();

      if (paidHistoryData.data != null) {
        showDialog<dynamic>(
            context: context,
            builder: (BuildContext contet) {
              return SuccessDialog(
                message: transactionInitialisation
                    .toString(), //Utils.getString(context, 'item_promote__success'),
                onPressed: () {
                  Navigator.pop(context, true);
                },
              );
            });
      } else {
        showDialog<dynamic>(
            context: context,
            builder: (BuildContext context) {
              return ErrorDialog(
                message: paidHistoryData.message,
              );
            });
      }
      /*Update your db with the init data received from initialization response,
          * Remaining bit will be sent via callback url*/
      return transactionInitialisation;
    } else {
      PsProgressDialog.dismissDialog();
      showDialog<dynamic>(
          context: context,
          builder: (BuildContext context) {
            return ErrorDialog(
              message: transactionInitialisation.toString(),
            );
          });
    }
  } catch (e) {
    //For now, console might be useful
    print('CAUGHT EXCEPTION: ' + e.toString());
    PsProgressDialog.dismissDialog();
    showDialog<dynamic>(
        context: context!,
        builder: (BuildContext context) {
          return ErrorDialog(message: transactionInitialisation.toString());
        });
  }
}

dynamic callWarningDialog(BuildContext context, String text) {
  showDialog<dynamic>(
      context: context,
      builder: (BuildContext context) {
        return WarningDialog(
          message: Utils.getString(text),
          onPressed: () {},
        );
      });
}

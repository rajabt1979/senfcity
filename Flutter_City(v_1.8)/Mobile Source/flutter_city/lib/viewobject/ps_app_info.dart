import 'package:fluttercity/viewobject/common/ps_object.dart';
import 'package:fluttercity/viewobject/delete_object.dart';
import 'package:fluttercity/viewobject/ps_app_version.dart';
import 'package:fluttercity/viewobject/user_info.dart';

class PSAppInfo extends PsObject<PSAppInfo> {
  PSAppInfo({
    this.psAppVersion,
    this.deleteObject,
    this.oneDay,
    this.currencySymbol,
    this.currencyShortForm,
    this.stripePublishableKey,
    this.payStackKey,
    this.paypalEnable,
    this.stripeEnable,
    this.razorEnable,
    this.payStackEnable,
    this.razorKey,
    this.userInfo,
    this.inAppPurchasedEnabled,
    this.inAppPurchasedPrdIdAndroid,
    this.inAppPurchasedPrdIdIOS,
  });

  PSAppVersion? psAppVersion;
  List<DeleteObject>? deleteObject;
  String? oneDay;
  String? currencySymbol;
  String? currencyShortForm;
  String? stripePublishableKey;
  String? razorKey;
  String? stripeEnable;
  String? paypalEnable;
  String? razorEnable;
  String? payStackEnable;
  String? payStackKey;
  UserInfo? userInfo;
  String? inAppPurchasedEnabled;
  String? inAppPurchasedPrdIdAndroid;
  String? inAppPurchasedPrdIdIOS;

  @override
  String getPrimaryKey() {
    return '';
  }

  @override
  PSAppInfo fromMap(dynamic dynamicData) {
    //if (dynamicData != null) {
      return PSAppInfo(
          psAppVersion: PSAppVersion().fromMap(dynamicData['version']),
          userInfo: UserInfo().fromMap(dynamicData['user_info']),
          deleteObject:
              DeleteObject().fromMapList(dynamicData['delete_history']),
          oneDay: dynamicData['oneday'],
          currencySymbol: dynamicData['currency_symbol'],
          currencyShortForm: dynamicData['currency_short_form'],
          stripePublishableKey: dynamicData['stripe_publishable_key'],
          payStackKey: dynamicData['paystack_key'],
          paypalEnable: dynamicData['paypal_enabled'],
          razorEnable: dynamicData['razor_enabled'],
          payStackEnable: dynamicData['paystack_enabled'],
          razorKey: dynamicData['razor_key'],
          stripeEnable: dynamicData['stripe_enabled'],
          inAppPurchasedEnabled: dynamicData['in_app_purchased_enabled'],
          inAppPurchasedPrdIdAndroid:
              dynamicData['in_app_purchased_prd_id_android'],
          inAppPurchasedPrdIdIOS: dynamicData['in_app_purchased_prd_id_ios']);
    // } else {
    //   return null;
    // }
  }

  @override
  Map<String, dynamic>? toMap(dynamic object) {
    if (object != null) {
      final Map<String, dynamic> data = <String, dynamic>{};
      data['version'] = PSAppVersion().fromMap(object.psAppVersion);
      data['user_info'] = PSAppVersion().fromMap(object.userInfo);
      data['delete_history'] = object.deleteObject.toList();
      data['oneday'] = object.oneDay;
      data['currency_symbol'] = object.currencySymbol;
      data['currency_short_form'] = object.currencyShortForm;
      data['stripe_publishable_key'] = object.stripePublishableKey;
      data['paystack_key'] = object.payStackKey;
      data['stripe_enabled'] = object.stripeEnable;
      data['paypal_enabled'] = object.paypalEnable;
      data['razor_enabled'] = object.razorEnable;
      data['paystack_enabled'] = object.payStackEnable;
      data['paypal_enabled'] = object.paypalEnable;
      data['in_app_purchased_enabled'] = object.inAppPurchasedEnabled;
      data['in_app_purchased_prd_id_android'] =
          object.inAppPurchasedPrdIdAndroid;
      data['in_app_purchased_prd_id_ios'] = object.inAppPurchasedPrdIdIOS;
      return data;
    } else {
      return null;
    }
  }

  @override
  List<PSAppInfo> fromMapList(List<dynamic> dynamicDataList) {
    final List<PSAppInfo> psAppInfoList = <PSAppInfo>[];

   // if (dynamicDataList != null) {
      for (dynamic json in dynamicDataList) {
        if (json != null) {
          psAppInfoList.add(fromMap(json));
        }
      }
   // }
    return psAppInfoList;
  }

  @override
  List<Map<String, dynamic>?> toMapList(List<dynamic> objectList) {
    final List<dynamic> dynamicList = <dynamic>[];
   // if (objectList != null) {
      for (dynamic data in objectList) {
        if (data != null) {
          dynamicList.add(toMap(data));
        }
      }
   // }

    return dynamicList as List<Map<String, dynamic>?> ;
  }
}

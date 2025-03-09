import 'package:flutter/cupertino.dart';
import 'package:fluttercity/viewobject/common/ps_holder.dart' show PsHolder;

class ShippingCityParameterHolder
    extends PsHolder<ShippingCityParameterHolder> {
  ShippingCityParameterHolder({
    @required this.shopId,
    @required this.countryId,
  });

  final String? shopId;
  final String? countryId;

  @override
  Map<String, dynamic> toMap() {
    final Map<String, dynamic> map = <String, dynamic>{};

    map['contact_id'] = shopId;
    map['country_id'] = countryId;

    return map;
  }

  @override
  ShippingCityParameterHolder fromMap(dynamic dynamicData) {
    return ShippingCityParameterHolder(
      shopId: dynamicData['contact_id'],
      countryId: dynamicData['country_id'],
    );
  }

  @override
  String getParamKey() {
    String key = '';

    if (shopId != '') {
      key += shopId!;
    }
    if (countryId != '') {
      key += countryId!;
    }
    return key;
  }
}

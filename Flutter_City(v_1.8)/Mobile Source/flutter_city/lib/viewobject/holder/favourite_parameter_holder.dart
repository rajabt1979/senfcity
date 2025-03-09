import 'package:flutter/cupertino.dart';
import 'package:fluttercity/viewobject/common/ps_holder.dart' show PsHolder;

class FavouriteParameterHolder extends PsHolder<FavouriteParameterHolder> {
  FavouriteParameterHolder({
    @required this.userId,
    @required this.itemId,
  });

  final String? userId;
  final String? itemId;

  @override
  Map<String, dynamic> toMap() {
    final Map<String, dynamic> map = <String, dynamic>{};

    map['user_id'] = userId;
    map['item_id'] = itemId;
    return map;
  }

  @override
  FavouriteParameterHolder fromMap(dynamic dynamicData) {
    return FavouriteParameterHolder(
      userId: dynamicData['user_id'],
      itemId: dynamicData['item_id'],
    );
  }

  @override
  String getParamKey() {
    String key = '';

    if (userId != '') {
      key += userId!;
    }
    if (itemId != '') {
      key += itemId!;
    }

    return key;
  }
}

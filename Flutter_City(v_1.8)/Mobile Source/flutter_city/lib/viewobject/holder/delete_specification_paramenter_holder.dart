import 'package:flutter/cupertino.dart';
import 'package:fluttercity/viewobject/common/ps_holder.dart' show PsHolder;

class DeleteSpecificationParameterHolder
    extends PsHolder<DeleteSpecificationParameterHolder> {
  DeleteSpecificationParameterHolder({
    @required this.itemId,
    @required this.id,
  });

  final String? itemId;
  final String? id;

  @override
  Map<String, dynamic> toMap() {
    final Map<String, dynamic> map = <String, dynamic>{};

    map['item_id'] = itemId;
    map['id'] = id;
    return map;
  }

  @override
  DeleteSpecificationParameterHolder fromMap(dynamic dynamicData) {
    return DeleteSpecificationParameterHolder(
      itemId: dynamicData['item_id'],
      id: dynamicData['id'],
    );
  }

  @override
  String getParamKey() {
    String key = '';

    if (itemId != '') {
      key += itemId!;
    }
    if (id != '') {
      key += id!;
    }

    return key;
  }
}

import 'package:fluttercity/viewobject/AttributeDetail.dart';
import 'package:fluttercity/viewobject/item.dart';

class AttributeDetailIntentHolder {
  const AttributeDetailIntentHolder({
    required this.product,
    required this.attributeDetail,
  });
  final Item product;
  final List<AttributeDetail> attributeDetail;
}

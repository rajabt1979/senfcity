import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';

class ItemListIntentHolder {
  const ItemListIntentHolder({
    required this.itemParameterHolder,
    required this.appBarTitle,
  });
  final ItemParameterHolder itemParameterHolder;
  final String appBarTitle;
}

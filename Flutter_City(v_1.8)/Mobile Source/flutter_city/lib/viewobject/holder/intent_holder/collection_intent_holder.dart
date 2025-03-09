import 'package:fluttercity/viewobject/item_collection_header.dart';

class CollectionIntentHolder {
  const CollectionIntentHolder({
    required this.itemCollectionHeader,
    required this.appBarTitle,
  });
  final ItemCollectionHeader itemCollectionHeader;
  final String appBarTitle;
}

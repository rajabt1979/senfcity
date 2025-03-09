import 'package:fluttercity/provider/gallery/gallery_provider.dart';
import 'package:fluttercity/viewobject/default_photo.dart';

class ItemEntryImageIntentHolder {
  const ItemEntryImageIntentHolder({
    required this.flag,
    required this.itemId,
    this.image,
    required this.provider,
  });
  final String? flag;
  final String? itemId;
  final DefaultPhoto? image;
  final GalleryProvider? provider;
}

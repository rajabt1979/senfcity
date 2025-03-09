import 'package:fluttercity/viewobject/basket.dart';

class CheckoutIntentHolder {
  const CheckoutIntentHolder({
    required this.basketList,
    required this.publishKey,
  });
  final List<Basket> basketList;
  final String publishKey;
}

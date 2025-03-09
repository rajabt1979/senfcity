import 'package:flutter/cupertino.dart';

class UserItemListIntentHolder {
  const UserItemListIntentHolder({
    @required this.userId,
    @required this.status,
    @required this.title,
  });
  final String? userId;
  final String? status;
  final String? title;
}

import 'dart:async';
import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/item_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/item.dart';

class ItemEntryProvider extends PsProvider {
  ItemEntryProvider(
      {@required ItemRepository? repo,
      @required this.psValueHolder,
      int limit = 0})
      : super(repo, limit) {
    _repo = repo;
    isDispose = false;
    print('Item Entry Provider: $hashCode');

    itemListStream = StreamController<PsResource<Item>>.broadcast();
    subscription = itemListStream.stream.listen((PsResource<Item> resource) {
      if ( resource.data != null) {
        _itemEntry = resource;
      }

      if (resource.status != PsStatus.BLOCK_LOADING &&
          resource.status != PsStatus.PROGRESS_LOADING) {
        isLoading = false;
      }

      if (!isDispose) {
        notifyListeners();
      }
    });
    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });
  }

  ItemRepository? _repo;
  PsValueHolder? psValueHolder;
  String? openingHour;
  String? closingHour;
  PsResource<Item> _itemEntry = PsResource<Item>(PsStatus.NOACTION, '', null);
  PsResource<Item> get item => _itemEntry;

 late StreamSubscription<PsResource<Item>> subscription;
 late StreamController<PsResource<Item>> itemListStream;

  // String selectedCategoryName = '';
  // String selectedSubCategoryName = '';
  // String selectedItemTypeName = '';
  // String selectedItemConditionName = '';
  // String selectedItemPriceTypeName = '';
  // String selectedItemCurrencySymbol = '';
  // String selectedItemLocation = '';
  // String selectedItemDealOption = '';
  String? categoryId = '';
  String? subCategoryId = '';
  String? cityId = '';
  String? statusId = '';
  String? isFeatured = '';
  String? isPromotion = '';
  String? itemLocationId = '';
  bool? isCheckBoxSelect = true;
  bool? isFeaturedCheckBoxSelect;
  bool? isPromotionCheckBoxSelect;
  String? checkOrNotShop = '1';
  String? itemId = '';

  @override
  void dispose() {
    subscription.cancel();
    isDispose = true;
    print('Item Entry Provider Dispose: $hashCode');
    super.dispose();
  }

  Future<dynamic> postItemEntry(
    Map<dynamic, dynamic> jsonMap,
  ) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();

    _itemEntry = await _repo!.postItemEntry(
        jsonMap, isConnectedToInternet, PsStatus.PROGRESS_LOADING);

    return _itemEntry;
    // return null;
  }

  Future<dynamic> getItemFromDB(String? itemId) async {
    isLoading = true;

    await _repo!.getItemFromDB(
        itemId ?? '', itemListStream, PsStatus.PROGRESS_LOADING);
  }
}

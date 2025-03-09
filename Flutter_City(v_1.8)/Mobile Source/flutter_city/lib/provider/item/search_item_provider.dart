import 'dart:async';
import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/item_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';
import 'package:fluttercity/viewobject/item.dart';
import '../../constant/ps_constants.dart';

class SearchItemProvider extends PsProvider {
  SearchItemProvider(
      {@required ItemRepository? repo, this.psValueHolder, int limit = 0})
      : super(repo, limit) {
    _repo = repo;
    print('SearchItemProvider : $hashCode');
    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });
    itemNameController = TextEditingController();

    itemListStream = StreamController<PsResource<List<Item>>>.broadcast();
    subscription =
        itemListStream.stream.listen((PsResource<List<Item>> resource) {
      updateOffset(resource.data!.length);

      _itemList = Utils.removeDuplicateObj<Item>(resource);

    if (psValueHolder != null) {
      if (itemParameterHolder.lat == '' && itemParameterHolder.lng == '') {
        itemParameterHolder.lat = psValueHolder!.locationLat;
        itemParameterHolder.lng = psValueHolder!.locationLng;
      }
    }
      // ignore: unnecessary_null_comparison
    //  if (itemParameterHolder != null) {
        //sortItemList();
      //}

      // if (resource.status != PsStatus.BLOCK_LOADING &&
      //     resource.status != PsStatus.PROGRESS_LOADING) {
      //   isLoading = false;
      // }

      if (!isDispose) {
        notifyListeners();
      }
    });



    itemNameController!.addListener(() {
      if ( itemNameController!.text != '') {
        itemParameterHolder.keyword = itemNameController!.text;
      } else {
        itemParameterHolder.keyword = '';
      }
    });
  }
  ItemRepository? _repo;
  PsValueHolder? psValueHolder;
  PsResource<List<Item>> _itemList =
      PsResource<List<Item>>(PsStatus.NOACTION, '', <Item>[]);

  PsResource<List<Item>> get itemList => _itemList;
 late StreamSubscription<PsResource<List<Item>>> subscription;
 late StreamController<PsResource<List<Item>>> itemListStream;
  TextEditingController? itemNameController;
 late ItemParameterHolder itemParameterHolder;

  bool isSwitchedFeaturedItem = false;
  bool isSwitchedDiscountPrice = false;

  bool isfirstRatingClicked = false;
  bool isSecondRatingClicked = false;
  bool isThirdRatingClicked = false;
  bool isfouthRatingClicked = false;
  bool isFifthRatingClicked = false;

  dynamic daoSubscription;
  String? _itemLocationId;

  @override
  void dispose() {
    subscription.cancel();
        if (daoSubscription != null) {
      daoSubscription.cancel();
    }
    isDispose = true;
    itemNameController!.dispose();
    Utils.psPrintRed('Search Item Provider Dispose: $hashCode');
    Utils.psPrintRed('ItemController Dispose ${itemNameController.hashCode}');
    super.dispose();
  }

  void sortItemListFromAtoZ() {
    _itemList.data!.sort(
      (Item prev, Item next) => prev.name!.compareTo(next.name!),
    );
  }

  void sortItemListFromZtoA() {
    _itemList.data!.sort(
      (Item prev, Item next) => next.name!.compareTo(prev.name!),
    );
  }

  // void sortItemList() {
  //   if (itemParameterHolder.isAtoZ == '1') {
  //     print('Sorting AtoZ');
  //     sortItemListFromAtoZ();
  //   } else if (itemParameterHolder.isAtoZ == '0') {
  //     sortItemListFromZtoA();
  //     print('Sorting ZtoA');
  //   } else {}
  // }

  void setRatingTo1AndHigher() {
    Utils.psPrintOrange('Rating 1 and Higher');
    isfirstRatingClicked = true;
    isSecondRatingClicked = false;
    isThirdRatingClicked = false;
    isfouthRatingClicked = false;
    isFifthRatingClicked = false;
    itemParameterHolder.ratingValue = PsConst.RATING_ONE;
    notifyListeners();
  }

  void setRatingTo2AndHigher() {
    Utils.psPrintOrange('Rating 2 and Higher');
    isfirstRatingClicked = false;
    isSecondRatingClicked = true;
    isThirdRatingClicked = false;
    isfouthRatingClicked = false;
    isFifthRatingClicked = false;
    itemParameterHolder.ratingValue = PsConst.RATING_TWO;
    notifyListeners();
  }

  void setRatingTo3AndHigher() {
    Utils.psPrintOrange('Rating 3 and Higher');
    isfirstRatingClicked = false;
    isSecondRatingClicked = false;
    isThirdRatingClicked = true;
    isfouthRatingClicked = false;
    isFifthRatingClicked = false;
    itemParameterHolder.ratingValue = PsConst.RATING_THREE;
    notifyListeners();
  }

  void setRatingTo4AndHigher() {
    Utils.psPrintOrange('Rating 4 and Higher');
    isfirstRatingClicked = false;
    isSecondRatingClicked = false;
    isThirdRatingClicked = false;
    isfouthRatingClicked = true;
    isFifthRatingClicked = false;
    itemParameterHolder.ratingValue = PsConst.RATING_FOUR;
    notifyListeners();
  }

  void setRatingTo5Only() {
    Utils.psPrintOrange('Rating 5 and Only');
    isfirstRatingClicked = false;
    isSecondRatingClicked = false;
    isThirdRatingClicked = false;
    isfouthRatingClicked = false;
    isFifthRatingClicked = true;
    itemParameterHolder.ratingValue = PsConst.RATING_FIVE;
    notifyListeners();
  }

  void setAllRatingFalse() {
    isfirstRatingClicked = false;
    isSecondRatingClicked = false;
    isThirdRatingClicked = false;
    isfouthRatingClicked = false;
    isFifthRatingClicked = false;
    notifyListeners();
  }

  void switchToFeaturedItem(bool value) {
    isSwitchedFeaturedItem = value;
    if (value) {
      itemParameterHolder.isFeatured = PsConst.IS_FEATURED;
    } else {
      itemParameterHolder.isFeatured = PsConst.ZERO;
    }
    notifyListeners();
  }

  void switchToDiscountItem(bool value) {
    isSwitchedDiscountPrice = value;
    if (value) {
      itemParameterHolder.isPromotion = PsConst.IS_PROMOTION;
    } else {
      itemParameterHolder.isPromotion = PsConst.ZERO;
    }
    notifyListeners();
  }

  void sortByLatest() {
    itemParameterHolder.isLatest = '1';
    itemParameterHolder.isAtoZ = '0';
    itemParameterHolder.isZtoA = '0';
    itemParameterHolder.isPopular = '0';

    itemParameterHolder.orderBy = PsConst.FILTERING__ADDED_DATE;
    itemParameterHolder.orderType = PsConst.FILTERING__DESC;
    notifyListeners();
  }

  void sortByPopular() {
    itemParameterHolder.isPopular = '1';
    itemParameterHolder.isAtoZ = '0';
    itemParameterHolder.isZtoA = '0';
    itemParameterHolder.isLatest = '0';

    itemParameterHolder.orderBy = PsConst.FILTERING_TRENDING;
    itemParameterHolder.orderType = PsConst.FILTERING__DESC;
    notifyListeners();
  }

  void sortByAtoZ() {
    itemParameterHolder.orderBy = '';
    itemParameterHolder.isAtoZ = '1';
    itemParameterHolder.isZtoA = '0';
    itemParameterHolder.isPopular = '0';
    itemParameterHolder.isLatest = '0';
    itemParameterHolder.orderType = PsConst.FILTERING__ASC;
    notifyListeners();
  }

  void sortByZtoA() {
    itemParameterHolder.orderBy = '';
    itemParameterHolder.isZtoA = '1';
    itemParameterHolder.isAtoZ = '0';
    itemParameterHolder.isPopular = '0';
    itemParameterHolder.isLatest = '0';
    itemParameterHolder.orderType = PsConst.FILTERING__DESC;
    notifyListeners();
  }

  Future<dynamic> loadItemListByKey(
      ItemParameterHolder itemParameterHolder) async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();
    isLoading = true;
    if (PsConfig.noFilterWithLocationOnMap) {
      if (itemParameterHolder.lat != '' &&
          itemParameterHolder.lng != '' &&
          itemParameterHolder.lat != null &&
          itemParameterHolder.lng != null) {
        _itemLocationId = itemParameterHolder.itemLocationId!;
        itemParameterHolder.itemLocationId = '';
      } else {
        if ( _itemLocationId != '') {
          itemParameterHolder.itemLocationId = _itemLocationId;
        }
      }
    }

   if (daoSubscription != null) {
      await daoSubscription.cancel();
    }
  daoSubscription=  await _repo!.getItemList(itemListStream, isConnectedToInternet, limit,
        offset, PsStatus.PROGRESS_LOADING, itemParameterHolder);
  }

  // Future<dynamic> nextItemListByKey(
  //     ItemParameterHolder itemParameterHolder) async {
  //   isConnectedToInternet = await Utils.checkInternetConnectivity();

  //   if (!isLoading && !isReachMaxData) {
  //     super.isLoading = true;

  //         if (PsConfig.noFilterWithLocationOnMap) {
  //     if (itemParameterHolder.lat != '' &&
  //         itemParameterHolder.lng != '' &&
  //         itemParameterHolder.lat != null &&
  //         itemParameterHolder.lng != null) {
  //       _itemLocationId = itemParameterHolder.itemLocationId!;
  //       itemParameterHolder.itemLocationId = '';
  //     } else {
  //       if ( _itemLocationId != '') {
  //         itemParameterHolder.itemLocationId = _itemLocationId;
  //       }
  //     }
  //   }
  //     await _repo!.getNextPageItemList(itemListStream, isConnectedToInternet,
  //         limit, offset, PsStatus.PROGRESS_LOADING, itemParameterHolder);
  //   }
  // }

    Future<dynamic> nextItemListByKey(
      ItemParameterHolder itemParameterHolder) async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();

    if (!isLoading && !isReachMaxData) {
      super.isLoading = true;
      print('*** Next Page Loading $limit $offset');
      await _repo!.getNextPageItemList(itemListStream, isConnectedToInternet,
          limit, offset, PsStatus.PROGRESS_LOADING, itemParameterHolder);
    }
  }

  Future<void> resetLatestItemList(
      ItemParameterHolder itemParameterHolder) async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();

    updateOffset(0);

    isLoading = true;
       if (daoSubscription != null) {
      await daoSubscription.cancel();
    }
  daoSubscription= 
    await _repo!.getItemList(itemListStream, isConnectedToInternet, limit,
        offset, PsStatus.PROGRESS_LOADING, itemParameterHolder);
  }
}

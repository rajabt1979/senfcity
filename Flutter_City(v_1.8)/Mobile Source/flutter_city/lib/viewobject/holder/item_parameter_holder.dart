import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/viewobject/common/ps_holder.dart';

class ItemParameterHolder extends PsHolder<dynamic> {
  ItemParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING__ADDED_DATE;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '';
    isAtoZ = '';
    isZtoA = '';
    isLatest = '';
    isPopular = '';
  }

  String? keyword;
  String? catId;
  String? subCatId;
  String? isFeatured;
  String? ratingValue;
  String? isPromotion;
  String? lat;
  String? lng;
  String? miles;
  String? orderBy;
  String? orderType;
  String? addedUserId;
  String? isPaid;
  String? itemLocationId;
  String? itemStatusId;
  String? isAtoZ;
  String? isZtoA;
  String? isLatest;
  String? isPopular;
  bool isFiltered() {
    return !((isPromotion == '0' || isPromotion == '') &&
        (isFeatured == '0' || isFeatured == '') &&
        (isLatest == '0' || isLatest == '') &&
        (isPopular == '0' || isPopular == '') &&
        (isAtoZ == '0' || isAtoZ == '') &&
        (isZtoA == '0' || isZtoA == '') &&
         lat == '' &&
         lng == '' &&
        ratingValue == '');
  }

  bool isCatAndSubCatFiltered() {
    return !(catId == '' && subCatId == '');
  }

  ItemParameterHolder getItemByCategoryIdParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING_TRENDING;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '';
     isLatest = '';

    return this;
  }

  ItemParameterHolder getItemByAddedUserIdParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING__ADDED_DATE;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '1';
     isLatest = '';

    return this;
  }

  ItemParameterHolder getPendingItemParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING__ADDED_DATE;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '0';
     isLatest = '';

    return this;
  }

  ItemParameterHolder getRejectedItemParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING__ADDED_DATE;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '3';
     isLatest = '';

    return this;
  }

  ItemParameterHolder getDisabledItemParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING__ADDED_DATE;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '2';

    return this;
  }

  ItemParameterHolder getDiscountParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = PsConst.ONE;
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING__ADDED_DATE;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '';
     isLatest = '';

    return this;
  }

  ItemParameterHolder getFeaturedParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = PsConst.ONE;
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING_FEATURE;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '';
     isLatest = '';

    return this;
  }

  ItemParameterHolder getTrendingParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING_TRENDING;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '';
    isPopular = '1';
     isLatest = '';

    return this;
  }

  ItemParameterHolder getLatestParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING__ADDED_DATE;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '';
    isLatest = '1';

    return this;
  }

  ItemParameterHolder resetParameterHolder() {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING__ADDED_DATE;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '';
     isLatest = '';

    return this;
  }

  @override
  Map<String, dynamic> toMap() {
    final Map<String, dynamic> map = <String, dynamic>{};
    map['keyword'] = keyword;
    map['cat_id'] = catId;
    map['sub_cat_id'] = subCatId;
    map['is_featured'] = isFeatured;
    map['rating_value'] = ratingValue;
    map['is_promotion'] = isPromotion;
    map['lat'] = lat;
    map['lng'] = lng;
    map['miles'] = miles;
    map['order_by'] = orderBy;
    map['order_type'] = orderType;
    map['added_user_id'] = addedUserId;
    map['is_paid'] = isPaid;
    map['is_latest'] = isLatest;
    map['item_location_id'] = itemLocationId;
    map['item_status_id'] = itemStatusId;
    return map;
  }

  @override
  dynamic fromMap(dynamic dynamicData) {
    keyword = '';
    catId = '';
    subCatId = '';
    isFeatured = '';
    ratingValue = '';
    isPromotion = '';
    lat = '';
    lng = '';
    miles = '';
    orderBy = PsConst.FILTERING__ADDED_DATE;
    orderType = PsConst.FILTERING__DESC;
    addedUserId = '';
    isPaid = '';
    itemLocationId = '';
    itemStatusId = '';
    isLatest = '';

    return this;
  }

  @override
  String getParamKey() {
    const String featured = 'featured';

    String result = '';

    if (keyword != '') {
      result += keyword !+ ':';
    }

    if (catId != '') {
      result += catId! + ':';
    }

    if (subCatId != '') {
      result += subCatId! + ':';
    }

    if (isFeatured != '' && isFeatured != '0') {
      result += featured + ':';
    }

    if (ratingValue != '' && ratingValue != '') {
      result += ratingValue! + ':';
    }
    if (isPromotion != '' && isPromotion != '') {
      result += isPromotion! + ':';
    }

    if (lat != '' && lat != '') {
      result += lat! + ':';
    }
    if (lng != '' && lng != '') {
      result += lng! + ':';
    }
    if (itemLocationId != '') {
      result += itemLocationId! + ':';
    }

    if (orderBy != '') {
      result += orderBy! + ':';
    }

    if (orderType != '') {
      result += orderType!;
    }

     if (isLatest != '') {
      result += isLatest!;
    }

    if (isPaid != '' && isPaid != '') {
      result += isPaid! + ':';
    }

    if (itemStatusId != '') {
      result += itemStatusId! + ':';
    }

    return result;
  }
}

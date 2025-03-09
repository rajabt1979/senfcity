import 'dart:async';

import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/city_info_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/city_info.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';

class CityInfoProvider extends PsProvider {
  CityInfoProvider(
      {@required CityInfoRepository? repo,
      @required this.psValueHolder,
      // @required this.ownerCode,
      int limit = 0})
      : super(repo, limit) {
    _repo = repo;

    //// ($ownerCode)
    print('CityInfo Provider: $hashCode');

    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });

    cityInfoListStream = StreamController<PsResource<CityInfo>>.broadcast();
    subscription =
        cityInfoListStream.stream.listen((PsResource<CityInfo> resource) async {
      _cityInfo = resource;

      if (resource.status != PsStatus.BLOCK_LOADING &&
          resource.status != PsStatus.PROGRESS_LOADING) {
        isLoading = false;
      }

      if (!isDispose) {
        // Update to share preference
        // To submit tax and shipping tax to transaction

        if ( cityInfo.data != null) {
         await replaceCityInfoData(
            // _cityInfo.data.overallTaxLabel,
            // _cityInfo.data.overallTaxValue,
            // _cityInfo.data.shippingTaxLabel,
            // _cityInfo.data.shippingTaxValue,
            // _cityInfo.data.shippingId,
            _cityInfo.data!.id!,
            _cityInfo.data!.name!,
            _cityInfo.data!.lat!,
            _cityInfo.data!.lng!,
            // _cityInfo.data.aboutPhone1,
          );

          // replaceCheckoutEnable(
          //     _cityInfo.data.paypalEnabled,
          //     _cityInfo.data.stripeEnabled,
          //     _cityInfo.data.codEmail,
          //     _cityInfo.data.banktransferEnabled,
          //     _cityInfo.data.standardShippingEnable,
          //     _cityInfo.data.zoneShippingEnable,
          //     _cityInfo.data.noShippingEnable);
          // replacePublishKey(_cityInfo.data.stripePublishableKey);

          notifyListeners();
        }
      }
    });
  }

  CityInfoRepository? _repo;
  PsValueHolder? psValueHolder;
  // String ownerCode;

  PsResource<CityInfo> _cityInfo =
      PsResource<CityInfo>(PsStatus.NOACTION, '', null);

  PsResource<CityInfo> get cityInfo => _cityInfo;
late  StreamSubscription<PsResource<CityInfo>> subscription;
 late StreamController<PsResource<CityInfo>> cityInfoListStream;
  @override
  void dispose() {
    subscription.cancel();
    isDispose = true;
    print('CityInfo Provider Dispose: $hashCode');
    super.dispose();
  }

  Future<dynamic> loadCityInfo() async {
    isLoading = true;
    isConnectedToInternet = await Utils.checkInternetConnectivity();

    await _repo!.getCityInfo(
        cityInfoListStream, isConnectedToInternet, PsStatus.PROGRESS_LOADING);
  }

  Future<dynamic> nextCityInfoList() async {
    if (!isLoading && !isReachMaxData) {
      super.isLoading = true;
      isConnectedToInternet = await Utils.checkInternetConnectivity();
      await _repo!.getCityInfo(
          cityInfoListStream, isConnectedToInternet, PsStatus.PROGRESS_LOADING);
    }
  }

  Future<void> resetCityInfoList() async {
    isLoading = true;
    isConnectedToInternet = await Utils.checkInternetConnectivity();
    await _repo!.getCityInfo(
        cityInfoListStream, isConnectedToInternet, PsStatus.BLOCK_LOADING);

    isLoading = false;
  }
}

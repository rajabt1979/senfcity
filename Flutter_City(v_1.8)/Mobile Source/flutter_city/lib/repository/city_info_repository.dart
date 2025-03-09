import 'dart:async';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/api/ps_api_service.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/db/city_info_dao.dart';
import 'package:fluttercity/viewobject/city_info.dart';
import 'Common/ps_repository.dart';

class CityInfoRepository extends PsRepository {
  CityInfoRepository(
      {required PsApiService psApiService,
      required CityInfoDao cityInfoDao}) {
    _psApiService = psApiService;
    _cityInfoDao = cityInfoDao;
  }

  String primaryKey = 'id';
late  PsApiService _psApiService;
late  CityInfoDao _cityInfoDao;

  Future<dynamic> insert(CityInfo cityInfo) async {
    return _cityInfoDao.insert(primaryKey, cityInfo);
  }

  Future<dynamic> update(CityInfo cityInfo) async {
    return _cityInfoDao.update(cityInfo);
  }

  Future<dynamic> delete(CityInfo cityInfo) async {
    return _cityInfoDao.delete(cityInfo);
  }

  Future<dynamic> getCityInfo(
      StreamController<PsResource<CityInfo>> cityInfoListStream,
      bool isConnectedToInternet,
      PsStatus status,
      {bool isLoadFromServer = true}) async {
    cityInfoListStream.sink.add(await _cityInfoDao.getOne(status: status));

    if (isConnectedToInternet) {
      final PsResource<CityInfo> _resource = await _psApiService.getCityInfo();

      if (_resource.status == PsStatus.SUCCESS) {
        await _cityInfoDao.deleteAll();
        await _cityInfoDao.insert(primaryKey, _resource.data!);
      } else {
        if (_resource.errorCode == PsConst.ERROR_CODE_10001) {
          await _cityInfoDao.deleteAll();
        }
      }
      cityInfoListStream.sink.add(await _cityInfoDao.getOne());
    }
  }
}

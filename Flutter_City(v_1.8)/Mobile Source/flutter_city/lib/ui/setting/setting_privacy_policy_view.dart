import 'package:flutter/material.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/provider/city_info/city_info_provider.dart';
import 'package:fluttercity/repository/city_info_repository.dart';
import 'package:fluttercity/ui/common/base/ps_widget_with_appbar.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:provider/provider.dart';

class SettingPrivacyPolicyView extends StatefulWidget {
  const SettingPrivacyPolicyView(
      {required this.checkType, required this.description});
  final int? checkType;
  final String? description;
  @override
  _SettingPrivacyPolicyViewState createState() {
    return _SettingPrivacyPolicyViewState();
  }
}

class _SettingPrivacyPolicyViewState extends State<SettingPrivacyPolicyView>
    with SingleTickerProviderStateMixin {
  final ScrollController _scrollController = ScrollController();

  CityInfoProvider? _cityInfoProvider;

  AnimationController? animationController;
  Animation<double>? animation;

  @override
  void dispose() {
    animationController!.dispose();
    animation = null;
    super.dispose();
  }

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(() {
      if (_scrollController.position.pixels ==
          _scrollController.position.maxScrollExtent) {
        _cityInfoProvider!.nextCityInfoList();
      }
    });

    animationController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 3),
    )..addListener(() => setState(() {}));

    animation = Tween<double>(
      begin: 0.0,
      end: 10.0,
    ).animate(animationController!);
  }

  CityInfoRepository? repo1;
  PsValueHolder? valueHolder;
  @override
  Widget build(BuildContext context) {
    repo1 = Provider.of<CityInfoRepository>(context);
    valueHolder = Provider.of<PsValueHolder?>(context);
    return PsWidgetWithAppBar<CityInfoProvider>(
        appBarTitle: widget.checkType == 2
            ? Utils.getString( 'cancellation_policy__toolbar_name')
            : widget.checkType == 1
                ? Utils.getString( 'terms_and_condition__toolbar_name')
                : widget.checkType == 3
                    ? Utils.getString( 'additional_info__toolbar_name')
                    : Utils.getString(
                         'privacy_policy__toolbar_name'),
        initProvider: () {
          return CityInfoProvider(
            repo: repo1,
            psValueHolder: valueHolder,
            // ownerCode: 'SettingPrivacyPolicyView'
          );
        },
        onProviderReady: (CityInfoProvider provider) {
          provider.loadCityInfo();
          _cityInfoProvider = provider;
        },
        builder:
            (BuildContext context, CityInfoProvider provider, Widget? child) {
          if (provider.cityInfo.data == null) {
            return Container();
          } else {
            return Padding(
              padding: const EdgeInsets.all(PsDimens.space16),
              child: SingleChildScrollView(
                child: Text(
                  widget.checkType == 1
                      ? widget.description!
                      : widget.checkType == 2
                          ? widget.description!
                          : widget.checkType == 3
                              ? widget.description!
                              : provider.cityInfo.data!.termsAndCondition!,
                  style: Theme.of(context)
                      .textTheme
                      .bodyText1!
                      .copyWith(height: 1.5, fontSize: PsDimens.space16),
                ),
              ),
            );
          }
        });
  }
}

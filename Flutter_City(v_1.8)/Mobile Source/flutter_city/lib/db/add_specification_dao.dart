import 'package:fluttercity/db/common/ps_dao.dart';
import 'package:fluttercity/viewobject/add_specification.dart';
import 'package:sembast/sembast.dart';

class AddSpecificationDao extends PsDao<AddSpecification> {
  AddSpecificationDao._() {
    init(AddSpecification());
  }

  static const String STORE_NAME = 'AddSpecification';
  final String _primaryKey = 'id';
  // Singleton instance
  static final AddSpecificationDao _singleton = AddSpecificationDao._();

  // Singleton accessor
  static AddSpecificationDao get instance => _singleton;

  @override
  String getStoreName() {
    return STORE_NAME;
  }

  @override
  String? getPrimaryKey(AddSpecification object) {
    return object.id;
  }

  @override
  Filter getFilter(AddSpecification object) {
    return Filter.equals(_primaryKey, object.id);
  }
}

import IconMinusCircle from '@/components/Icon/IconMinusCircle';
import IconPencil from '@/components/Icon/IconPencil';
import { useGetDynamicOptionCOAQuery, useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import {
  useGetSettingOptionMuMutation,
  usePutSettingOptionMutation,
} from '@/store/api/setting/settingApiSlice';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { OptionDataType, OptionDataUpdateType } from '@/types';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { keyboard } from '@testing-library/user-event/dist/keyboard';
import Tippy from '@tippyjs/react';
import { useTranslation } from 'react-i18next';
import { useEffect, useState } from 'react';
import {
  Control,
  Controller,
  FieldArrayPath,
  FieldPath,
  FieldValues,
  SubmitHandler,
  useFieldArray,
  useForm,
  UseFormRegister,
  UseFormSetValue,
  useWatch,
} from 'react-hook-form';
import { useDispatch } from 'react-redux';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import SelectSearch from 'react-select';
import 'tippy.js/dist/tippy.css';

interface Section {
  sectionName: string;
  coaCode: string;
  showChild: boolean;
  subSection: Section[];
}

interface FormValues {
  config: Section[];
}

interface SectionFieldsProps {
  control: Control<FormValues>;
  register: UseFormRegister<FormValues>;
  setValue: UseFormSetValue<FormValues>;
  path: string;
  nestingLevel: number;
  isEditing: boolean;
  parentShowChildPath?: FieldPath<FormValues>;
  parentCoaCode?: string;
}

interface SingleSectionProps {
  control: Control<FormValues>;
  register: UseFormRegister<FormValues>;
  setValue: UseFormSetValue<FormValues>;
  itemPath: string;
  item: any;
  index: number;
  isEditing: boolean;
  parentCoaCode: string;
  nestingLevel: number;
  onRemove: (index: number) => void;
}

interface CoaSelectProps {
  value: string | null | undefined;
  onChange: (value: string) => void;
  isDisabled?: boolean;
  parentCoaCode?: string;
}

interface ConfigFormProps {
  activeTab: 'balancesheet' | 'profitloss' | 'cashflow';
  isEditing: boolean;
  onSubmitSuccess: () => void;
  onCancelEdit: () => void;
}

const CoaSelect: React.FC<CoaSelectProps> = ({ value, onChange, isDisabled, parentCoaCode }) => {
  const { t } = useTranslation();
  const [searchAccount, setSearchAccount] = useState('');
  const hasParentFilter = !!parentCoaCode;

  let listParams: { [key: string]: any } = {};

  // sub-section
  if (hasParentFilter) {
    listParams = { parent: parentCoaCode };
  } else {
    // top-level section
    listParams = { page: -1, pageSize: -1 };
  }

  const { data: listResponse, isLoading: isLoadingList } = useGetDynamicOptionCOAQuery(listParams);
  // const newOptionsList = listResponse?.data?.map((item: any) => ({
  //   ...item,
  //   label: `[${item.desc}] ${item.label}`,
  // }));
  const newOptionsList = listResponse?.data?.map((item: any) => ({
    ...item,
    label: `[${item.desc}] ${item.label}`,
  }));

  const optionsList = newOptionsList || [];

  const { data: selectedItemResponse, isLoading: isLoadingSelected } = useGetDynamicOptionCOAQuery(
    { keyword: value as string },
    {
      skip: !value, // skip if no value is currently selected
    }
  );

  // find full object for the current value
  const selectedItem = selectedItemResponse?.data?.find((item: any) => item.desc === value) ?? null;

  const isLoading =
    isLoadingList ||
    // wait fetching is done
    (!!value && !selectedItem && isLoadingSelected);

  // combine lists
  let selectedOption = optionsList.find((option: any) => option.desc === value) ?? null;

  if (!selectedOption && selectedItem) {
    selectedOption = selectedItem;
  }

  // create final list for select
  const finalOptions = [...optionsList];
  if (selectedOption && !finalOptions.some((item: any) => item.desc === selectedOption.desc)) {
    finalOptions.unshift(selectedOption);
  }

  return (
    <SelectSearch
      options={finalOptions}
      onInputChange={(value) => setSearchAccount(value)}
      onChange={(selectedOption: any) => {
        onChange(selectedOption ? selectedOption.desc : '');
      }}
      className="w-full font-normal text-black"
      value={selectedOption}
      isDisabled={isDisabled}
      isLoading={isLoading}
      isClearable
    />
  );
};

const SingleSection: React.FC<SingleSectionProps> = ({
  control,
  register,
  setValue,
  itemPath,
  item,
  index,
  isEditing,
  parentCoaCode,
  nestingLevel,
  onRemove,
}) => {
  const { t } = useTranslation();
  const currentCoaCode = useWatch({
    control,
    name: `${itemPath}.coaCode` as FieldPath<FormValues>,
    defaultValue: item.coaCode,
  }) as string;

  return (
    <div key={item.id} className="mb-4 bg-white dark:bg-[#1b2e4b] border border-gray-100 dark:border-gray-800 rounded-lg p-4 shadow-sm transition-all hover:shadow-md">
      <div className="flex items-start gap-4">
        {/* Section Name & Controls */}
        <div className="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">{t('settings_page.report_config.form.section_name')}</label>
            <div className="flex gap-2">
              <input
                {...register(`${itemPath}.sectionName` as FieldPath<FormValues>)}
                placeholder={t('settings_page.report_config.form.section_placeholder')}
                className="form-input w-full font-semibold text-gray-800 dark:text-gray-200"
                disabled={!isEditing}
              />
              {isEditing && (
                <Tippy content={t('settings_page.report_config.form.show_child')}>
                  <label className="flex items-center justify-center w-10 h-10 bg-gray-50 dark:bg-gray-700 rounded-md cursor-pointer border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <input
                      type="checkbox"
                      className="form-checkbox w-5 h-5 text-primary rounded"
                      {...register(`${itemPath}.showChild` as FieldPath<FormValues>)}
                    />
                  </label>
                </Tippy>
              )}
            </div>
          </div>

          <div>
            <label className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">{t('settings_page.report_config.form.mapping_coa')}</label>
            {isEditing ? (
              <div className="relative">
                <Controller
                  name={`${itemPath}.coaCode` as FieldPath<FormValues>}
                  control={control}
                  render={({ field }) => (
                    <CoaSelect
                      value={field.value as string}
                      onChange={field.onChange}
                      isDisabled={!isEditing}
                      parentCoaCode={parentCoaCode}
                    />
                  )}
                />
              </div>
            ) : (
              <div className="form-input bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700">
                {item.coaCode || <span className="italic opacity-50">{t('settings_page.report_config.form.no_coa_mapped')}</span>}
              </div>
            )}
          </div>
        </div>

        {/* Action Buttons */}
        {isEditing && (
          <div className="pt-6">
            <Tippy content={t('settings_page.report_config.form.remove_section')}>
              <button
                type="button"
                onClick={() => onRemove(index)}
                className="p-2 text-red-500 hover:bg-red-50 rounded-full transition-colors"
              >
                <IconMinusCircle className="w-5 h-5" />
              </button>
            </Tippy>
          </div>
        )}
      </div>

      {/* Recursive Sub-sections */}
      <div className="mt-4">
        <SectionFields
          path={`${itemPath}.subSection`}
          nestingLevel={nestingLevel + 1}
          control={control}
          register={register}
          setValue={setValue}
          isEditing={isEditing}
          parentShowChildPath={`${itemPath}.showChild` as FieldPath<FormValues>}
          parentCoaCode={currentCoaCode}
        />
      </div>
    </div>
  );
};

const SectionFields: React.FC<SectionFieldsProps> = ({
  control,
  register,
  setValue,
  path,
  nestingLevel,
  isEditing,
  parentShowChildPath,
  parentCoaCode,
}) => {
  const { t } = useTranslation();
  const { fields, append, remove } = useFieldArray({
    control,
    name: path as FieldArrayPath<FormValues>,
  });

  const handleAddSubSection = () => {
    append({
      sectionName: '',
      coaCode: '',
      showChild: false,
      subSection: [],
    });

    if (parentShowChildPath) {
      setValue(parentShowChildPath, true);
    }
  };

  return (
    <div className={nestingLevel > 0 ? 'ml-6 pl-6 border-l-2 border-dashed border-gray-200 dark:border-gray-700 mt-4' : 'space-y-4'}>
      {fields.map((item, index) => (
        <SingleSection
          key={`${path}.${index}`}
          control={control}
          register={register}
          setValue={setValue}
          itemPath={`${path}.${index}`}
          item={item}
          index={index}
          isEditing={isEditing}
          parentCoaCode={parentCoaCode || ''}
          nestingLevel={nestingLevel}
          onRemove={remove}
        />
      ))}
      {isEditing && (
        <button
          type="button"
          onClick={handleAddSubSection}
          className={`
            flex items-center gap-2 font-medium transition-all
            ${nestingLevel === 0
              ? 'w-full justify-center py-3 border-2 border-dashed border-primary/30 text-primary hover:bg-primary/5 rounded-lg hover:border-primary'
              : 'text-sm text-primary hover:text-primary/80 mt-2'}
          `}
        >
          <div className="flex items-center justify-center w-5 h-5 rounded-full bg-primary/10">
            <IconPencil className="w-3 h-3" />
          </div>
          {nestingLevel === 0 ? t('settings_page.report_config.form.add_top_section') : t('settings_page.report_config.form.add_sub_section')}
        </button>
      )}
    </div>
  );
};

const ConfigForm: React.FC<ConfigFormProps> = ({
  activeTab,
  isEditing,
  onSubmitSuccess,
  onCancelEdit,
}) => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const [currentSetting, setCurrentSetting] = useState<OptionDataType | null>(null);
  const [getSetting, { data: settingData, isLoading: isLoadingSetting, isSuccess }] =
    useGetSettingOptionMuMutation();
  const [update] = usePutSettingOptionMutation();

  const {
    control,
    register,
    handleSubmit,
    setValue,
    reset,
    formState: { isSubmitting },
  } = useForm<FormValues>({
    defaultValues: {
      config: [],
    },
  });

  const reportEndpoints = {
    balancesheet: 'CFG_REPORT_BALANCESHEET',
    profitloss: 'CFG_REPORT_PROFITLOSS',
    cashflow: 'CFG_REPORT_CASHFLOW',
  };

  // filter empty data
  const filterValidSections = (sections: Section[]): Section[] => {
    if (!Array.isArray(sections)) {
      return [];
    }

    const itemsWithFilteredChildren = sections.map((item) => {
      if (typeof item !== 'object' || item === null) {
        return null;
      }
      return {
        ...item,
        subSection: filterValidSections(item.subSection || []),
      };
    });

    return itemsWithFilteredChildren.filter((item): item is Section => {
      if (typeof item !== 'object' || item === null) {
        return false;
      }

      const hasSectionName = item.sectionName && item.sectionName.trim() !== '';
      const hasCoaCode = item.coaCode && item.coaCode.trim() !== '';
      const hasValidChildren = item.subSection && item.subSection.length > 0;

      return hasSectionName || hasCoaCode || hasValidChildren;
    });
  };

  useEffect(() => {
    const endpointToFetch = reportEndpoints[activeTab];
    getSetting(endpointToFetch);
  }, [activeTab, getSetting]);

  useEffect(() => {
    if (isSuccess && settingData && settingData.data) {
      setCurrentSetting(settingData.data);
      let parsedConfig: Section[] = [];

      if (settingData.data.optionValue) {
        try {
          const dataToParse = settingData.data.optionValue;

          if (typeof dataToParse === 'object' && dataToParse !== null) {
            parsedConfig = dataToParse as Section[];
          } else if (typeof dataToParse === 'string') {
            try {
              parsedConfig = JSON.parse(dataToParse);
            } catch (e) {
              const jsonString = dataToParse.replace(/'/g, '"');
              parsedConfig = JSON.parse(jsonString);
            }
          }
        } catch (error) {
          toastMessage(t('settings_page.report_config.toast.parse_error'), 'error');
          console.error('Parsing error:', error);
          parsedConfig = [];
        }
      }

      const filteredConfig = filterValidSections(parsedConfig);
      reset({ config: filteredConfig });
    }
  }, [settingData, isSuccess, reset, activeTab]);

  const onSubmit: SubmitHandler<FormValues> = async (formData) => {
    try {
      if (!currentSetting) {
        toastMessage(t('settings_page.report_config.toast.no_data'), 'error');
        return;
      }

      const newConfigArray = formData.config;
      const newOptionValue = JSON.stringify(newConfigArray);
      const updatePayload: OptionDataUpdateType = {
        optionId: currentSetting.optionId,
        optionName: currentSetting.optionName || '',
        optionLabel: currentSetting.optionLabel,
        optionValue: newOptionValue,
        optionDesc: currentSetting.optionDesc,
      };

      const response = await update(updatePayload);

      responseCallback(
        response,
        (data: any) => {
          onSubmitSuccess();
          setTimeout(() => {
            navigate('/settings/report');
          }, 1000);
        },
        (error: any) => {
          toastMessage(error.message || t('settings_page.report_config.toast.update_failed'), 'error');
        }
      );
    } catch (err: any) {
      toastMessage(err.message || t('settings_page.report_config.toast.update_error'), 'error');
    }
  };

  if (isLoadingSetting) {
    return <p>Loading settings...</p>;
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <SectionFields
        path="config"
        nestingLevel={0}
        control={control}
        register={register}
        setValue={setValue}
        isEditing={isEditing}
        parentCoaCode={''}
      />

      {isEditing && (
        <div className="mt-8 flex w-full justify-end gap-4">
          <button type="button" onClick={onCancelEdit} className="btn btn-outline-danger">
            {t('settings_page.report_config.buttons.cancel')}
          </button>
          <button type="submit" className="btn btn-primary w-1/6" disabled={isSubmitting}>
            {isSubmitting ? t('settings_page.report_config.buttons.saving') : t('settings_page.report_config.buttons.save_changes')}
          </button>
        </div>
      )}
    </form>
  );
};

const Form = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const [activeTab, setActiveTab] = useState<'balancesheet' | 'profitloss' | 'cashflow'>('balancesheet');
  const [isEditing, setIsEditing] = useState(false);

  useEffect(() => {
    dispatch(setPageTitle(t('settings_page.report_config.title')));
    dispatch(setTitle(t('settings_page.report_config.title')));
    dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('settings_page.title'), t('settings_page.report_config.title')]));
  }, [dispatch, t]);

  const handleSubmitSuccess = () => {
    setIsEditing(false);
  };

  const handleCancelEdit = () => {
    setIsEditing(false);
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        {/* Tab Navigation */}
        <div className="bg-gray-100/50 dark:bg-black/20 p-1 rounded-xl flex items-center">
          <button
            type="button"
            onClick={() => {
              if (!isEditing) setActiveTab('balancesheet');
            }}
            disabled={isEditing}
            className={`flex-1 py-2 px-6 rounded-lg text-sm font-semibold transition-all ${activeTab === 'balancesheet'
              ? 'bg-white dark:bg-gray-800 text-primary shadow-sm'
              : 'text-gray-500 hover:text-gray-700 hover:bg-white/50'
              } ${isEditing && activeTab !== 'balancesheet' ? 'opacity-50 cursor-not-allowed' : ''}`}
          >
            {t('settings_page.report_config.tabs.balance_sheet')}
          </button>
          <button
            type="button"
            onClick={() => {
              if (!isEditing) setActiveTab('profitloss');
            }}
            disabled={isEditing}
            className={`flex-1 py-2 px-6 rounded-lg text-sm font-semibold transition-all ${activeTab === 'profitloss'
              ? 'bg-white dark:bg-gray-800 text-primary shadow-sm'
              : 'text-gray-500 hover:text-gray-700 hover:bg-white/50'
              } ${isEditing && activeTab !== 'profitloss' ? 'opacity-50 cursor-not-allowed' : ''}`}
          >
            {t('settings_page.report_config.tabs.profit_loss')}
          </button>
          <button
            type="button"
            onClick={() => {
              if (!isEditing) setActiveTab('cashflow');
            }}
            disabled={isEditing}
            className={`flex-1 py-2 px-6 rounded-lg text-sm font-semibold transition-all ${activeTab === 'cashflow'
              ? 'bg-white dark:bg-gray-800 text-primary shadow-sm'
              : 'text-gray-500 hover:text-gray-700 hover:bg-white/50'
              } ${isEditing && activeTab !== 'cashflow' ? 'opacity-50 cursor-not-allowed' : ''}`}
          >
            {t('settings_page.report_config.tabs.cash_flow', 'Cash Flow')}
          </button>
        </div>

        {!isEditing && (
          <button
            type="button"
            className="btn btn-primary shadow-lg shadow-primary/30"
            onClick={() => setIsEditing(true)}
          >
            <IconPencil className="w-4 h-4 mr-2" />
            {t('settings_page.report_config.buttons.edit_config')}
          </button>
        )}
      </div>

      {/* Main Config Area */}
      <div className={`transition-opacity duration-300 ${isEditing ? 'opacity-100' : 'opacity-90'}`}>
        <ConfigForm
          key={activeTab}
          activeTab={activeTab}
          isEditing={isEditing}
          onSubmitSuccess={handleSubmitSuccess}
          onCancelEdit={handleCancelEdit}
        />
      </div>
    </div>
  );
};

export default Form;

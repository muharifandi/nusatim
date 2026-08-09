package com.nusatim.partner.features.profile.ui.marketing

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.profile.domain.usecase.GetMarketingMaterialDetailUseCase
import com.nusatim.partner.features.profile.domain.usecase.GetMarketingMaterialsUseCase
import com.nusatim.partner.features.profile.ui.marketing.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class MarketingViewModel @Inject constructor(
    private val getMarketingMaterialsUseCase: GetMarketingMaterialsUseCase,
    private val getMarketingMaterialDetailUseCase: GetMarketingMaterialDetailUseCase
) : BaseViewModel<MarketingState, MarketingIntent, MarketingEffect>(MarketingState()) {

    init {
        processIntent(MarketingIntent.LoadMarketingMaterials())
    }

    override fun processIntent(intent: MarketingIntent) {
        when (intent) {
            is MarketingIntent.LoadMarketingMaterials -> loadMaterials(intent.category)
            is MarketingIntent.LoadMarketingMaterialDetail -> loadDetail(intent.id)
        }
    }

    private fun loadMaterials(category: String?) {
        viewModelScope.launch {
            getMarketingMaterialsUseCase(category).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, materials = result.data, error = null) 
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { MarketingEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun loadDetail(id: Int) {
        viewModelScope.launch {
            getMarketingMaterialDetailUseCase(id).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, selectedDetail = result.data, error = null) 
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { MarketingEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }
}

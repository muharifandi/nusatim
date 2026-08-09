package com.nusatim.partner.features.leads.ui.customers.detail

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.leads.domain.usecase.GetCustomerDetailUseCase
import com.nusatim.partner.features.leads.domain.usecase.UpdateCustomerProgressUseCase
import com.nusatim.partner.features.leads.domain.usecase.UpdateCustomerUseCase
import com.nusatim.partner.features.leads.ui.customers.detail.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class CustomerDetailViewModel @Inject constructor(
    private val getCustomerDetailUseCase: GetCustomerDetailUseCase,
    private val updateCustomerUseCase: UpdateCustomerUseCase,
    private val updateCustomerProgressUseCase: UpdateCustomerProgressUseCase
) : BaseViewModel<CustomerDetailState, CustomerDetailIntent, CustomerDetailEffect>(CustomerDetailState()) {

    override fun processIntent(intent: CustomerDetailIntent) {
        when (intent) {
            is CustomerDetailIntent.LoadCustomer -> loadCustomer(intent.id)
            is CustomerDetailIntent.UpdateCustomer -> updateCustomer(intent.id, intent.request)
            is CustomerDetailIntent.UpdateProgress -> updateProgress(intent.id, intent.progress)
        }
    }

    private fun loadCustomer(id: Int) {
        viewModelScope.launch {
            getCustomerDetailUseCase(id).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, customer = result.data, error = null) 
                    }
                    is ResultState.Error -> setState { 
                        copy(isLoading = false, error = result.message) 
                    }
                }
            }
        }
    }

    private fun updateCustomer(id: Int, request: Map<String, Any?>) {
        viewModelScope.launch {
            updateCustomerUseCase(id, request).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false, customer = result.data) }
                        sendEffect { CustomerDetailEffect.SuccessUpdate }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { CustomerDetailEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun updateProgress(id: Int, progress: Int) {
        viewModelScope.launch {
            updateCustomerProgressUseCase(id, progress).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false, customer = result.data) }
                        sendEffect { CustomerDetailEffect.SuccessUpdate }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { CustomerDetailEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }
}

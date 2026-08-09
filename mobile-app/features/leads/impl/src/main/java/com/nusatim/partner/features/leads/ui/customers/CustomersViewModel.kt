package com.nusatim.partner.features.leads.ui.customers

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.leads.domain.usecase.GetCustomersUseCase
import com.nusatim.partner.features.leads.ui.customers.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class CustomersViewModel @Inject constructor(
    private val getCustomersUseCase: GetCustomersUseCase
) : BaseViewModel<CustomersState, CustomersIntent, CustomersEffect>(CustomersState()) {

    init {
        processIntent(CustomersIntent.LoadCustomers())
    }

    override fun processIntent(intent: CustomersIntent) {
        when (intent) {
            is CustomersIntent.LoadCustomers -> loadCustomers(intent.page)
        }
    }

    private fun loadCustomers(page: Int) {
        viewModelScope.launch {
            getCustomersUseCase(page = page).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, customersResponse = result.data, error = null) 
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { CustomersEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }
}

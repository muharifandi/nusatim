package com.nusatim.partner.features.home.ui.dashboard

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.home.domain.usecase.GetDashboardUseCase
import com.nusatim.partner.features.home.ui.dashboard.state.DashboardEffect
import com.nusatim.partner.features.home.ui.dashboard.state.DashboardIntent
import com.nusatim.partner.features.home.ui.dashboard.state.DashboardState
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class DashboardViewModel @Inject constructor(
    private val getDashboardUseCase: GetDashboardUseCase
) : BaseViewModel<DashboardState, DashboardIntent, DashboardEffect>(DashboardState()) {

    init {
        processIntent(DashboardIntent.LoadDashboard)
    }

    override fun processIntent(intent: DashboardIntent) {
        when (intent) {
            is DashboardIntent.LoadDashboard -> loadDashboard()
            is DashboardIntent.RefreshDashboard -> loadDashboard()
        }
    }

    private fun loadDashboard() {
        viewModelScope.launch {
            getDashboardUseCase().collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState {
                        copy(isLoading = false, dashboard = result.data, error = null)
                    }
                    is ResultState.Error -> setState {
                        copy(isLoading = false, error = result.message)
                    }
                }
            }
        }
    }
}

package com.nusatim.partner.features.auth.status.ui

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.common.security.SessionManager
import com.nusatim.partner.core.model.ResultState
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch
import javax.inject.Inject

data class ApprovalStatusState(
    val status: String? = null,
    val rejectionReason: String? = null,
    val isLoading: Boolean = false,
    val error: String? = null,
    val isApproved: Boolean = false,
    val isLoggedOut: Boolean = false
)

@HiltViewModel
class ApprovalStatusViewModel @Inject constructor(
    private val authRepository: AuthRepository,
    private val sessionManager: SessionManager
) : ViewModel() {

    private val _state = MutableStateFlow(ApprovalStatusState(status = sessionManager.getPartnerStatus()))
    val state: StateFlow<ApprovalStatusState> = _state.asStateFlow()

    fun refreshStatus() {
        viewModelScope.launch {
            authRepository.getCurrentUser().collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> _state.update { it.copy(isLoading = true, error = null) }
                    is ResultState.Success -> {
                        val status = result.data.status
                        sessionManager.savePartnerStatus(status)
                        _state.update { 
                            it.copy(
                                isLoading = false, 
                                status = status,
                                rejectionReason = result.data.rejectionReason,
                                isApproved = status == "approved"
                            ) 
                        }
                    }
                    is ResultState.Error -> {
                        _state.update { it.copy(isLoading = false, error = result.message) }
                    }
                }
            }
        }
    }

    fun logout() {
        viewModelScope.launch {
            authRepository.logout().collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> _state.update { it.copy(isLoading = true) }
                    is ResultState.Success -> {
                        sessionManager.clearSession()
                        _state.update { it.copy(isLoading = false, isLoggedOut = true) }
                    }
                    is ResultState.Error -> {
                        // Even if logout fails on server, clear local session
                        sessionManager.clearSession()
                        _state.update { it.copy(isLoading = false, isLoggedOut = true) }
                    }
                }
            }
        }
    }

    fun dismissError() {
        _state.update { it.copy(error = null) }
    }
}

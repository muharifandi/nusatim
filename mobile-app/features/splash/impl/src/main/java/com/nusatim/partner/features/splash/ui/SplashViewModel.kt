package com.nusatim.partner.features.splash.ui

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.common.security.SessionManager
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.splash.ui.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class SplashViewModel @Inject constructor(
    private val sessionManager: SessionManager,
    private val authRepository: AuthRepository,
) : BaseViewModel<SplashState, SplashIntent, SplashEffect>(SplashState()) {

    init {
        checkSession()
    }

    override fun processIntent(intent: SplashIntent) {
        when (intent) {
            is SplashIntent.CheckSession -> checkSession()
            is SplashIntent.Retry -> checkSession()
        }
    }

    private fun checkSession() {
        viewModelScope.launch {
            if (!sessionManager.isIntroCompleted()) {
                delay(1000)
                sendEffect { SplashEffect.NavigateToIntro }
                return@launch
            }

            val token = sessionManager.getAuthToken()
            if (token.isNullOrBlank()) {
                delay(1000) // Minimal splash time
                sendEffect { SplashEffect.NavigateToLogin }
                return@launch
            }

            authRepository.getCurrentUser().collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> { /* Keep showing logo */ }
                    is ResultState.Success -> {
                        val status = result.data.status
                        sessionManager.savePartnerStatus(status)
                        if (status == "approved") {
                            sendEffect { SplashEffect.NavigateToHome }
                        } else {
                            sendEffect { SplashEffect.NavigateToApprovalStatus }
                        }
                    }
                    is ResultState.Error -> {
                        if (result.message.contains("401", ignoreCase = true) || 
                            result.message.contains("login", ignoreCase = true)) {
                            sessionManager.clearSession()
                            sendEffect { SplashEffect.NavigateToLogin }
                        } else {
                            sendEffect { SplashEffect.ShowError(result.message) }
                        }
                    }
                }
            }
        }
    }
}

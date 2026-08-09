package com.nusatim.partner.features.login.ui

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.common.security.SessionManager
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.login.domain.usecase.LoginUseCase
import com.nusatim.partner.features.login.ui.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class LoginViewModel @Inject constructor(
    private val loginUseCase: LoginUseCase,
    private val sessionManager: SessionManager,
) : BaseViewModel<LoginState, LoginIntent, LoginEffect>(LoginState()) {

    override fun processIntent(intent: LoginIntent) {
        when (intent) {
            is LoginIntent.EmailChanged -> {
                setState { copy(email = intent.value, error = null) }
            }
            is LoginIntent.PasswordChanged -> {
                setState { copy(password = intent.value, error = null) }
            }
            is LoginIntent.Submit -> {
                login()
            }
            is LoginIntent.NavigateToRegister -> {
                sendEffect { LoginEffect.NavigateToRegister }
            }
            is LoginIntent.NavigateToForgotPassword -> {
                sendEffect { LoginEffect.NavigateToForgotPassword }
            }
            is LoginIntent.DismissError -> {
                setState { copy(error = null) }
            }
            else -> {}
        }
    }

    private fun login() {
        val email = state.value.email
        val password = state.value.password

        // Validasi Email Format
        if (!android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
            setState { copy(error = "Format email tidak valid. Gunakan contoh: nama@email.com") }
            return
        }

        viewModelScope.launch {
            val request = mapOf(
                "email" to email,
                "password" to password,
                "device_name" to android.os.Build.MODEL
            )

            loginUseCase(request).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true, error = null) }
                    is ResultState.Success -> {
                        sessionManager.saveAuthToken(result.data.token)
                        sessionManager.savePartnerName(result.data.partner.name)
                        sessionManager.savePartnerStatus(result.data.partner.status)
                        setState { copy(isLoading = false, isSuccess = true) }

                        if (result.data.partner.status == "approved") {
                            sendEffect { LoginEffect.NavigateToHome }
                        } else {
                            sendEffect { LoginEffect.NavigateToApprovalStatus }
                        }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                    }
                }
            }
        }
    }
}

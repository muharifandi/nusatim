package com.nusatim.partner.features.register.ui

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.register.domain.usecase.RegisterUseCase
import com.nusatim.partner.features.register.ui.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import javax.inject.Inject

@HiltViewModel
class RegisterViewModel @Inject constructor(
    private val registerUseCase: RegisterUseCase,
) : BaseViewModel<RegisterState, RegisterIntent, RegisterEffect>(RegisterState()) {

    override fun processIntent(intent: RegisterIntent) {
        when (intent) {
            is RegisterIntent.NextStep -> handleNextStep()
            is RegisterIntent.PreviousStep -> handlePreviousStep()
            
            is RegisterIntent.NameChanged -> setState { copy(name = intent.value) }
            is RegisterIntent.EmailChanged -> setState { copy(email = intent.value) }
            is RegisterIntent.PasswordChanged -> setState { copy(password = intent.value) }
            is RegisterIntent.PasswordConfirmationChanged -> setState { copy(passwordConfirmation = intent.value) }
            
            is RegisterIntent.ProfilePhotoPicked -> setState { copy(profilePhoto = intent.file) }
            is RegisterIntent.KtpPicked -> setState { copy(ktp = intent.file) }
            is RegisterIntent.NpwpPicked -> setState { copy(npwp = intent.file) }
            
            is RegisterIntent.BankNameChanged -> setState { copy(bankName = intent.value) }
            is RegisterIntent.BankAccountNumberChanged -> setState { copy(bankAccountNumber = intent.value) }
            is RegisterIntent.BankAccountHolderChanged -> setState { copy(bankAccountHolder = intent.value) }
            
            is RegisterIntent.AgreementChanged -> setState { copy(isAgreed = intent.value) }
            is RegisterIntent.Submit -> handleRegistration()
        }
    }

    private fun handleNextStep() {
        val currentStep = state.value.currentStep
        if (currentStep < state.value.totalSteps) {
            setState { copy(currentStep = currentStep + 1) }
        }
    }

    private fun handlePreviousStep() {
        val currentStep = state.value.currentStep
        if (currentStep > 1) {
            setState { copy(currentStep = currentStep - 1) }
        }
    }

    private fun handleRegistration() {
        viewModelScope.launch {
            val currentState = state.value
            
            val nameBody = currentState.name.toRequestBody("text/plain".toMediaTypeOrNull())
            val emailBody = currentState.email.toRequestBody("text/plain".toMediaTypeOrNull())
            val passwordBody = currentState.password.toRequestBody("text/plain".toMediaTypeOrNull())
            val passwordConfirmBody = currentState.passwordConfirmation.toRequestBody("text/plain".toMediaTypeOrNull())
            val bankNameBody = currentState.bankName.toRequestBody("text/plain".toMediaTypeOrNull())
            val bankAccountNumBody = currentState.bankAccountNumber.toRequestBody("text/plain".toMediaTypeOrNull())
            val bankAccountHolderBody = currentState.bankAccountHolder.toRequestBody("text/plain".toMediaTypeOrNull())
            val agreementBody = currentState.isAgreed.toString().toRequestBody("text/plain".toMediaTypeOrNull())

            val profilePhotoPart = currentState.profilePhoto?.let {
                val requestFile = it.asRequestBody("image/*".toMediaTypeOrNull())
                MultipartBody.Part.createFormData("profile_photo", it.name, requestFile)
            } ?: return@launch

            val ktpPart = currentState.ktp?.let {
                val requestFile = it.asRequestBody("image/*".toMediaTypeOrNull())
                MultipartBody.Part.createFormData("ktp", it.name, requestFile)
            } ?: return@launch

            val npwpPart = currentState.npwp?.let {
                val requestFile = it.asRequestBody("image/*".toMediaTypeOrNull())
                MultipartBody.Part.createFormData("npwp", it.name, requestFile)
            }

            registerUseCase(
                nameBody, emailBody, passwordBody, passwordConfirmBody,
                profilePhotoPart, ktpPart, npwpPart,
                bankNameBody, bankAccountNumBody, bankAccountHolderBody,
                agreementBody
            ).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true, error = null) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false, isSuccess = true) }
                        sendEffect { RegisterEffect.NavigateToLogin }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                    }
                }
            }
        }
    }
}

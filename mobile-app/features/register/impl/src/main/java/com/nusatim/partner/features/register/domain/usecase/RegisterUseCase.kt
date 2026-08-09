package com.nusatim.partner.features.register.domain.usecase

import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PartnerResponse
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody
import okhttp3.RequestBody
import javax.inject.Inject

class RegisterUseCase @Inject constructor(
    private val repository: AuthRepository
) {
    operator fun invoke(
        name: RequestBody,
        email: RequestBody,
        password: RequestBody,
        passwordConfirmation: RequestBody,
        profilePhoto: MultipartBody.Part,
        ktp: MultipartBody.Part,
        npwp: MultipartBody.Part?,
        bankName: RequestBody,
        bankAccountNumber: RequestBody,
        bankAccountHolder: RequestBody,
        agreementAccepted: RequestBody
    ): Flow<ResultState<PartnerResponse>> {
        return repository.register(
            name, email, password, passwordConfirmation,
            profilePhoto, ktp, npwp,
            bankName, bankAccountNumber, bankAccountHolder,
            agreementAccepted
        )
    }
}

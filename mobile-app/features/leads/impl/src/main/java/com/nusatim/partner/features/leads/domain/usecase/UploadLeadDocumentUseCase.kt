package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.LeadDocumentResponse
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody
import javax.inject.Inject

class UploadLeadDocumentUseCase @Inject constructor(
    private val repository: LeadsRepository
) {
    operator fun invoke(id: Int, file: MultipartBody.Part, originalName: String? = null): Flow<ResultState<LeadDocumentResponse>> {
        return repository.uploadLeadDocument(id, file, originalName)
    }
}

package com.nusatim.partner.features.home.domain.usecase

import com.nusatim.partner.core.domain.repository.NotificationsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.NotificationResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetNotificationsUseCase @Inject constructor(
    private val repository: NotificationsRepository
) {
    operator fun invoke(page: Int? = null, perPage: Int? = null): Flow<ResultState<PagedBaseResponse<NotificationResponse>>> {
        return repository.getNotifications(page, perPage)
    }
}

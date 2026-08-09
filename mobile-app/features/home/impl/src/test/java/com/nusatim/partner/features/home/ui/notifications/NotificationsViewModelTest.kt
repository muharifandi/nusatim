package com.nusatim.partner.features.home.ui.notifications

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.NotificationResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.UnreadCountResponse
import com.nusatim.partner.features.home.domain.usecase.*
import com.nusatim.partner.features.home.ui.notifications.state.NotificationsIntent
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.flow.flowOf
import kotlinx.coroutines.test.UnconfinedTestDispatcher
import kotlinx.coroutines.test.resetMain
import kotlinx.coroutines.test.runTest
import kotlinx.coroutines.test.setMain
import org.junit.After
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

@OptIn(ExperimentalCoroutinesApi::class)
class NotificationsViewModelTest {

    private val getNotificationsUseCase: GetNotificationsUseCase = mockk()
    private val getUnreadCountUseCase: GetUnreadNotificationsCountUseCase = mockk()
    private val markAsReadUseCase: MarkNotificationAsReadUseCase = mockk()
    private val markAllAsReadUseCase: MarkAllNotificationsAsReadUseCase = mockk()

    private lateinit var viewModel: NotificationsViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        
        // Mock init call
        coEvery { getUnreadCountUseCase() } returns flowOf(ResultState.Loading)
        
        viewModel = NotificationsViewModel(
            getNotificationsUseCase,
            getUnreadCountUseCase,
            markAsReadUseCase,
            markAllAsReadUseCase
        )
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load notifications successful, state should have data`() = runTest {
        val response = mockk<PagedBaseResponse<NotificationResponse>>()
        coEvery { getNotificationsUseCase(any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(response)
        )

        viewModel.processIntent(NotificationsIntent.LoadNotifications(1))

        assertEquals(response, viewModel.state.value.notificationsResponse)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when load unread count successful, state should have count`() = runTest {
        val response = UnreadCountResponse(5)
        coEvery { getUnreadCountUseCase() } returns flowOf(
            ResultState.Loading,
            ResultState.Success(response)
        )

        viewModel.processIntent(NotificationsIntent.LoadUnreadCount)

        assertEquals(5, viewModel.state.value.unreadCount)
    }

    @Test
    fun `when mark all as read successful, unread count should be 0`() = runTest {
        coEvery { markAllAsReadUseCase() } returns flowOf(
            ResultState.Loading,
            ResultState.Success("Success")
        )
        // Mock refresh call after mark all as read
        coEvery { getNotificationsUseCase(any(), any()) } returns flowOf(ResultState.Success(mockk()))

        viewModel.processIntent(NotificationsIntent.MarkAllAsRead)

        assertEquals(0, viewModel.state.value.unreadCount)
    }
}

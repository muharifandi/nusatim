package com.nusatim.partner.features.profile.ui.support.detail

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.model.dto.SupportTicketResponse
import com.nusatim.partner.features.profile.databinding.FragmentSupportTicketDetailBinding
import com.nusatim.partner.features.profile.ui.support.SupportTicketsViewModel
import com.nusatim.partner.features.profile.ui.support.state.SupportTicketsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class SupportTicketDetailFragment : BaseFragment<FragmentSupportTicketDetailBinding>() {

    private val viewModel: SupportTicketsViewModel by viewModels()
    private val ticketId by lazy { arguments?.getInt("id") ?: -1 }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = getString(com.nusatim.partner.features.profile.R.string.support_detail_title)
            setNavigationOnClickListener {
                findNavController().navigateUp()
            }
        }

        if (ticketId != -1) {
            viewModel.processIntent(SupportTicketsIntent.LoadDetail(ticketId))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    state.selectedDetail?.let { renderDetail(it) }
                }
            }
        }
    }

    private fun renderDetail(item: SupportTicketResponse) {
        binding.tvSubject.text = item.subject
        binding.tvDescription.text = item.description
        binding.tvDate.text = item.createdAt
        
        binding.btnStatus.text = item.status.replaceFirstChar { it.uppercase() }
        val statusColor = when (item.status.lowercase()) {
            "open" -> com.nusatim.partner.core.ui.R.color.status_info
            "in_progress" -> com.nusatim.partner.core.ui.R.color.status_warning
            "resolved" -> com.nusatim.partner.core.ui.R.color.status_success
            "closed" -> com.nusatim.partner.core.ui.R.color.status_neutral
            else -> com.nusatim.partner.core.ui.R.color.status_neutral
        }
        binding.btnStatus.setBackgroundTintList(android.content.res.ColorStateList.valueOf(
            androidx.core.content.ContextCompat.getColor(requireContext(), statusColor)
        ))

        if (!item.resolutionNote.isNullOrEmpty()) {
            binding.cardResolution.visibility = android.view.View.VISIBLE
            binding.tvResolutionNote.text = item.resolutionNote
        } else {
            binding.cardResolution.visibility = android.view.View.GONE
        }
    }
}

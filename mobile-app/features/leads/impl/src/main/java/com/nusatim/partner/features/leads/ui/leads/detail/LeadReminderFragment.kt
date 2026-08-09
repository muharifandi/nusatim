package com.nusatim.partner.features.leads.ui.leads.detail

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.databinding.FragmentLeadReminderBinding
import com.nusatim.partner.features.leads.ui.leads.detail.state.LeadDetailIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class LeadReminderFragment : BaseFragment<FragmentLeadReminderBinding>() {

    private val viewModel: LeadDetailViewModel by viewModels({ requireParentFragment() })
    private val adapter by lazy {
        LeadReminderAdapter { reminder ->
            val leadId = viewModel.state.value.lead?.id ?: return@LeadReminderAdapter
            viewModel.processIntent(LeadDetailIntent.CompleteReminder(leadId, reminder.id))
        }
    }

    override fun onInitViews() {
        binding.rvReminders.layoutManager = LinearLayoutManager(requireContext())
        binding.rvReminders.adapter = adapter
        
        binding.fabAddReminder.setOnClickListener {
            // TODO: Show Add Reminder BottomSheet
        }
        
        val leadId = viewModel.state.value.lead?.id
        if (leadId != null) {
            viewModel.processIntent(LeadDetailIntent.LoadReminders(leadId))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    adapter.submitList(state.reminders)

                    val isEmpty = state.reminders.isEmpty() && !state.isLoading
                    binding.layoutEmpty.root.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                    if (isEmpty) {
                        binding.layoutEmpty.tvErrorMessage.text = getString(com.nusatim.partner.features.leads.R.string.lead_tab_reminder_empty)
                        binding.layoutEmpty.btnRetry.visibility = android.view.View.GONE
                    }
                }
            }
        }
    }
}

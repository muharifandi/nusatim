package com.nusatim.partner.features.leads.ui.leads.detail

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.databinding.FragmentLeadTimelineBinding
import com.nusatim.partner.features.leads.ui.leads.detail.state.LeadDetailIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class LeadTimelineFragment : BaseFragment<FragmentLeadTimelineBinding>() {

    private val viewModel: LeadDetailViewModel by viewModels({ requireParentFragment() })
    private val adapter by lazy { LeadActivityAdapter() }

    override fun onInitViews() {
        binding.rvActivities.layoutManager = LinearLayoutManager(requireContext())
        binding.rvActivities.adapter = adapter
        
        binding.fabAddNote.setOnClickListener {
            // TODO: Show Add Note Dialog
        }
        
        val leadId = viewModel.state.value.lead?.id
        if (leadId != null) {
            viewModel.processIntent(LeadDetailIntent.LoadActivities(leadId))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    adapter.submitList(state.activities)

                    val isEmpty = state.activities.isEmpty() && !state.isLoading
                    binding.layoutEmpty.root.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                    if (isEmpty) {
                        binding.layoutEmpty.tvErrorMessage.text = getString(com.nusatim.partner.features.leads.R.string.lead_tab_timeline_empty)
                        binding.layoutEmpty.btnRetry.visibility = android.view.View.GONE
                    }
                }
            }
        }
    }
}

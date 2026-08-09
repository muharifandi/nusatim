package com.nusatim.partner.features.profile.ui.marketing

import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.model.dto.MarketingMaterialResponse
import com.nusatim.partner.features.profile.databinding.FragmentMarketingMaterialsBinding
import com.nusatim.partner.features.profile.ui.marketing.state.MarketingIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class MarketingMaterialsFragment : BaseFragment<FragmentMarketingMaterialsBinding>() {

    private val viewModel: MarketingViewModel by viewModels()
    private val adapter by lazy {
        MarketingAdapter(
            onItemClick = { material ->
                findNavController().navigate("partner://marketing/detail?id=${material.id}".toUri())
            },
            onActionClick = { material ->
                if (material.isFileBased) {
                    // TODO: Trigger Download
                } else {
                    // TODO: Copy to Clipboard
                }
            }
        )
    }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = "Marketing Center"
            setNavigationOnClickListener {
                findNavController().navigateUp()
            }
        }

        binding.rvMaterials.layoutManager = LinearLayoutManager(requireContext())
        binding.rvMaterials.adapter = adapter

        binding.swipeRefresh.setOnRefreshListener {
            viewModel.processIntent(MarketingIntent.LoadMarketingMaterials())
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    binding.swipeRefresh.isRefreshing = state.isLoading
                    
                    val listItems = mutableListOf<MarketingListItem>()
                    state.materials.forEach { (categoryLabel, materials) ->
                        listItems.add(MarketingListItem.Header(categoryLabel))
                        listItems.addAll(materials.map { MarketingListItem.Item(it) })
                    }
                    adapter.submitList(listItems)
                    
                    val isEmpty = state.materials.isEmpty() && !state.isLoading
                    binding.layoutEmpty.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                    binding.rvMaterials.visibility = if (isEmpty) android.view.View.GONE else android.view.View.VISIBLE
                }
            }
        }
    }
}
